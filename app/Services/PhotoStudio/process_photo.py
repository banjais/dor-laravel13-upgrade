#!/usr/bin/env python3

import argparse
import json
import math
import os
import sys
from io import BytesIO
from pathlib import Path

import cv2
import numpy as np
from PIL import Image, ImageOps
from rembg import new_session, remove


FACE_MODEL = Path.home() / ".u2net" / "face_detection_yunet_2026may.onnx"
MAX_SIDE = 2200


def straighten(image):
    """Use face eye landmarks for gentle, non-cropping rotation."""
    if not FACE_MODEL.is_file():
        raise RuntimeError("Face detection model is unavailable.")

    try:
        h, w = image.shape[:2]
        detector = cv2.FaceDetectorYN.create(
            str(FACE_MODEL), "", (w, h),
            0.6, 0.3, 5000
        )
        detector.setInputSize((w, h))
        _, faces = detector.detect(image)

        if faces is None or len(faces) == 0:
            return image, 0.0, False

        # Use the largest detected face.
        face = max(faces, key=lambda f: float(f[2] * f[3]))

        # YuNet eye landmarks: indices 4-7.
        eye1 = (float(face[4]), float(face[5]))
        eye2 = (float(face[6]), float(face[7]))

        if abs(eye2[0] - eye1[0]) < 1:
            return image, 0.0, True

        left, right = sorted((eye1, eye2), key=lambda p: p[0])
        angle = math.degrees(math.atan2(
            right[1] - left[1],
            right[0] - left[0]
        ))

        if not (1.5 <= abs(angle) <= 12):
            return image, 0.0, True

        # Rotate the complete image on an expanded canvas.
        center = (w / 2, h / 2)
        matrix = cv2.getRotationMatrix2D(center, angle, 1.0)

        cos_a = abs(matrix[0, 0])
        sin_a = abs(matrix[0, 1])
        new_w = int(h * sin_a + w * cos_a)
        new_h = int(h * cos_a + w * sin_a)

        matrix[0, 2] += new_w / 2 - center[0]
        matrix[1, 2] += new_h / 2 - center[1]

        rotated = cv2.warpAffine(
            image, matrix, (new_w, new_h),
            flags=cv2.INTER_CUBIC,
            borderMode=cv2.BORDER_REFLECT
        )
        return rotated, round(angle, 2), True

    except Exception as exc:
        raise RuntimeError(
            "Face detection could not be completed. Please try again."
        ) from exc



def head_neck_crop(image):
    """
    Create a conservative square head-and-neck crop.
    Uses a neutral background outside the source image; never mirrors edges.
    Fails safely when a reliable face cannot be detected.
    """
    if not FACE_MODEL.is_file():
        return image, False

    try:
        h, w = image.shape[:2]
        detector = cv2.FaceDetectorYN.create(
            str(FACE_MODEL), "", (w, h), 0.6, 0.3, 5000
        )
        detector.setInputSize((w, h))
        _, faces = detector.detect(image)

        if faces is None or len(faces) == 0:
            return image, False

        face = max(faces, key=lambda f: float(f[2] * f[3]))
        x, y, fw, fh = [float(v) for v in face[:4]]

        if fw < 20 or fh < 20:
            return image, False

        if x < 0 or y < 0 or x + fw > w or y + fh > h:
            return image, False

        # Face occupies about 40% of the square frame height.
        size = int(round(max(fh * 2.45, fw * 2.8)))
        if size < 80:
            return image, False

        center_x = x + fw / 2.0
        left = int(round(center_x - size / 2.0))
        top = int(round(y - fh * 0.32))

        # Neutral padding prevents fragmented/mirrored edge artefacts.
        crop = np.full((size, size, 3), 250, dtype=np.uint8)

        src_left = max(0, left)
        src_top = max(0, top)
        src_right = min(w, left + size)
        src_bottom = min(h, top + size)

        if src_right <= src_left or src_bottom <= src_top:
            return image, False

        dst_left = src_left - left
        dst_top = src_top - top
        dst_right = dst_left + (src_right - src_left)
        dst_bottom = dst_top + (src_bottom - src_top)

        crop[dst_top:dst_bottom, dst_left:dst_right] = (
            image[src_top:src_bottom, src_left:src_right]
        )

        # Confirm the detected face remains fully inside the crop.
        face_left = x - left
        face_top = y - top
        face_right = face_left + fw
        face_bottom = face_top + fh

        if (
            face_left < 0 or face_top < 0 or
            face_right > size or face_bottom > size
        ):
            return image, False

        return crop, True

    except Exception:
        return image, False



def enhance(image, profile="standard"):
    """Conservative enhancement with a separate Office Chief profile."""
    if profile == "office_chief":
        # Preserve original facial details for official portraits.
        return image.copy()

    denoise = 3
    contrast = 1.3
    sharpen_amount = 1.35

    # Reduce noise while preserving facial and clothing details.
    denoised = cv2.fastNlMeansDenoisingColored(
        image, None, denoise, denoise, 7, 21
    )

    # Improve local contrast without aggressive colour changes.
    lab = cv2.cvtColor(denoised, cv2.COLOR_BGR2LAB)
    light, a, b = cv2.split(lab)

    clahe = cv2.createCLAHE(
        clipLimit=contrast,
        tileGridSize=(8, 8)
    )
    light = clahe.apply(light)

    enhanced = cv2.cvtColor(
        cv2.merge((light, a, b)),
        cv2.COLOR_LAB2BGR
    )

    # Gently compress extreme neutral highlights.
    hsv = cv2.cvtColor(enhanced, cv2.COLOR_BGR2HSV)
    h, s, v = cv2.split(hsv)

    highlight_mask = (v > 232) & (s < 45)
    v_float = v.astype(np.float32)
    v_float[highlight_mask] = (
        232 + (v_float[highlight_mask] - 232) * 0.45
    )
    v = np.clip(v_float, 0, 255).astype(np.uint8)

    enhanced = cv2.cvtColor(
        cv2.merge((h, s, v)),
        cv2.COLOR_HSV2BGR
    )

    # Mild sharpening; avoid aggressive edge enhancement.
    blurred = cv2.GaussianBlur(enhanced, (0, 0), 1.0)
    sharpened = cv2.addWeighted(
        enhanced, sharpen_amount, blurred,
        1.0 - sharpen_amount, 0
    )

    return np.clip(sharpened, 0, 255).astype(np.uint8)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--input", required=True)
    parser.add_argument("--output", required=True)
    parser.add_argument(
        "--profile",
        choices=("standard", "office_chief"),
        default="standard"
    )
    parser.add_argument(
        "--framing",
        choices=("full", "head_neck"),
        default="full"
    )
    args = parser.parse_args()

    source = Path(args.input)
    destination = Path(args.output)

    if not source.is_file():
        raise ValueError("Input photo does not exist.")

    if not destination.parent.is_dir():
        raise ValueError("Output directory does not exist.")

    if source.stat().st_size > 10 * 1024 * 1024:
        raise ValueError("Input photo exceeds the processing limit.")

    with Image.open(source) as original:
        if original.format not in ("JPEG", "PNG"):
            raise ValueError("Only JPEG and PNG images are supported.")

        original = ImageOps.exif_transpose(original).convert("RGB")

        if original.width < 100 or original.height < 100:
            raise ValueError("Image dimensions are too small.")

        # Limit working dimensions for predictable memory use.
        original.thumbnail(
            (MAX_SIDE, MAX_SIDE),
            Image.Resampling.LANCZOS
        )

        rgb = np.array(original)

    bgr = cv2.cvtColor(rgb, cv2.COLOR_RGB2BGR)

    # Straightening disabled: preserve the original photo orientation.
    rotation = 0.0
    _, _, face_detected = straighten(bgr)

    if not face_detected:
        raise ValueError("No human face detected. Please upload a valid photo.")

    framing_applied = False
    if args.framing == "head_neck":
        bgr, framing_applied = head_neck_crop(bgr)
        if not framing_applied:
            raise ValueError(
                "A clear face could not be detected for Head & Neck framing. "
                "Please use a clear, front-facing portrait photo."
            )
        face_detected = True

    # Keep the existing enhancement pipeline.
    bgr = enhance(bgr, args.profile)

    processed_rgb = cv2.cvtColor(bgr, cv2.COLOR_BGR2RGB)

    prepared = BytesIO()
    Image.fromarray(processed_rgb).save(prepared, format="PNG")
    input_bytes = prepared.getvalue()

    # Safe fallback: do not remove background when no face is detected.
    # This protects templates, landscapes and non-person images.
    segmentation_used = False
    fallback_reason = "face_not_detected"

    if face_detected:
        try:
            session = new_session("u2net_human_seg")
            if args.profile == "office_chief":
                # Generate only the segmentation mask.
                # Keep the original photo pixels unchanged.
                mask_bytes = remove(
                    input_bytes,
                    session=session,
                    only_mask=True,
                    post_process_mask=False
                )

                with Image.open(BytesIO(mask_bytes)) as mask_result:
                    mask = mask_result.convert("L")

                expected_size = (
                    processed_rgb.shape[1],
                    processed_rgb.shape[0]
                )
                if mask.size != expected_size:
                    raise ValueError("Segmentation mask dimensions do not match the photo.")

                candidate = Image.fromarray(processed_rgb).convert("RGBA")
                candidate.putalpha(mask)

            else:
                result_bytes = remove(
                    input_bytes,
                    session=session,
                    post_process_mask=True
                )

                with Image.open(BytesIO(result_bytes)) as result:
                    candidate = result.convert("RGBA")

            alpha = np.asarray(candidate.getchannel("A"))
            foreground_ratio = float(np.mean(alpha > 128))

            # Reject empty or nearly full-frame segmentation masks.
            if 0.02 <= foreground_ratio <= 0.98:
                foreground = candidate
                segmentation_used = True
                fallback_reason = None
            else:
                foreground = Image.fromarray(processed_rgb).convert("RGBA")
                fallback_reason = "implausible_segmentation_mask"

        except Exception:
            foreground = Image.fromarray(processed_rgb).convert("RGBA")
            fallback_reason = "segmentation_failed"
    else:
        foreground = Image.fromarray(processed_rgb).convert("RGBA")

    # Preserve the full photo and its proportions.
    foreground.thumbnail(
        (800, 800),
        Image.Resampling.LANCZOS
    )

    background = Image.new(
        "RGBA", (800, 800), (250, 250, 250, 255)
    )

    x = (800 - foreground.width) // 2
    y = (800 - foreground.height) // 2
    background.alpha_composite(foreground, (x, y))

    final_image = background.convert("RGB")

    temporary = destination.with_name(destination.name + ".tmp")
    final_image.save(
        temporary,
        format="JPEG",
        quality=92,
        optimize=True
    )
    os.replace(temporary, destination)

    print(json.dumps({
        "status": "success",
        "width": 800,
        "height": 800,
        "background": "#FAFAFA",
        "model": "u2net_human_seg",
        "framing": args.framing,
        "profile": args.profile,
        "framing_applied": framing_applied,
        "face_detected": face_detected,
        "segmentation_used": segmentation_used,
        "fallback_reason": fallback_reason,
        "rotation_degrees": rotation,
        "enhancements": (
            [] if args.profile == "office_chief" else [
                "gentle_denoise",
                "local_contrast",
                "highlight_compression",
                "mild_sharpening"
            ]
        ),
        "output_bytes": destination.stat().st_size
    }))


if __name__ == "__main__":
    try:
        main()
    except Exception as exc:
        print(json.dumps({
            "status": "error",
            "message": str(exc)
        }), file=sys.stderr)
        sys.exit(1)
