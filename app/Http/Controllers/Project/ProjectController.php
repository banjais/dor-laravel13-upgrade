<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\ConfigurationController;
use App\Models\Language;
use App\Models\Project as ProjectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProjectController extends ConfigurationController
{
    public $abs_site_url;
    public $rel_site_url;
    public $site_name;

    public function __construct()
    {
        $this->abs_site_url = ConfigurationController::abs_site_url();
        $this->rel_site_url = ConfigurationController::rel_site_url();
        $this->site_name = ConfigurationController::site_name();
    }

    private function errorView()
    {
        return view('error')
            ->with('abs_site_url', $this->abs_site_url)
            ->with('rel_site_url', $this->rel_site_url)
            ->with('site_name', $this->site_name);
    }

    private function projectUser()
    {
        $user = Auth::user();

        if (!$user || (int) $user->user_type_id !== 3 || empty($user->project_id)) {
            abort(403);
        }

        return $user;
    }

    /**
     * Project User Dashboard
     */
    public function getIndex()
    {
        try {
            $user = $this->projectUser();

            $project = ProjectModel::with(['detailEng', 'detailNep'])
                ->where('id', $user->project_id)
                ->where('is_deleted', '0')
                ->first();

            if (!$project) {
                return $this->errorView();
            }

            return view('projects.project.index')
                ->with('project', $project)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        } catch (Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                throw $e;
            }

            report($e);
            return $this->errorView();
        }
    }

    /**
     * Project User Edit Form
     */
    public function getEditProject($id)
    {
        try {
            $user = $this->projectUser();

            // A Project User can edit only their own assigned project.
            if ((int) $id !== (int) $user->project_id) {
                abort(403);
            }

            $projectData = ProjectModel::where('id', $user->project_id)
                ->where('is_deleted', '0')
                ->first();

            if (!$projectData) {
                return $this->errorView();
            }

            $languages = Language::getActiveLanguages();

            // Include every active language, even if its detail row is missing.
            $projects = DB::table('language')
                ->leftJoin('project_detail', function ($join) use ($id) {
                    $join->on('project_detail.language_id', '=', 'language.id')
                        ->where('project_detail.project_id', '=', $id);
                })
                ->where('language.is_active', '1')
                ->where('language.is_deleted', '0')
                ->select(
                    'language.id as language_id',
                    'language.language_name',
                    DB::raw((int) $id . ' as project_id'),
                    'project_detail.id as project_detail_id',
                    'project_detail.project_heading',
                    'project_detail.project_description',
                    'project_detail.project_page_title'
                )
                ->orderBy('language.id')
                ->get();

            return view('projects.project.edit')
                ->with('projects', $projects)
                ->with('projectData', $projectData)
                ->with('languages', $languages)
                ->with('abs_site_url', $this->abs_site_url)
                ->with('rel_site_url', $this->rel_site_url)
                ->with('site_name', $this->site_name);
        } catch (Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                throw $e;
            }

            report($e);
            return $this->errorView();
        }
    }

    /**
     * Update only the authenticated Project User's own project.
     */
    public function updateProject(Request $request, $id)
    {
        try {
            $user = $this->projectUser();

            if ((int) $id !== (int) $user->project_id) {
                abort(403);
            }

            $project = ProjectModel::where('id', $user->project_id)
                ->where('is_deleted', '0')
                ->first();

            if (!$project) {
                return $this->errorView();
            }

            $languages = Language::getActiveLanguages();

            $rules = [
                'project_url' => 'required|string|max:255',
                'project_latitude' => 'nullable|numeric',
                'project_longitude' => 'nullable|numeric',
                'project_zoom' => 'nullable|numeric',
                'is_active' => 'required|in:0,1',
            ];

            foreach ($languages as $language) {
                $rules['project_heading_' . $language->id] = 'required|string';
                $rules['project_description_' . $language->id] = 'required|string';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }

            $projectUrl = strtolower(trim($request->input('project_url')));
            $projectUrl = preg_replace('/[^a-z0-9-]+/', '-', $projectUrl);
            $projectUrl = trim(preg_replace('/-+/', '-', $projectUrl), '-');

            if ($projectUrl === '' || !preg_match('/[a-z0-9]/', $projectUrl)) {
                return Redirect::back()
                    ->withInput()
                    ->with('type', 'error')
                    ->with('message', 'Please enter a valid project URL.');
            }

            $duplicate = ProjectModel::where('project_url', $projectUrl)
                ->where('office_id', $user->office_id)
                ->where('id', '!=', $project->id)
                ->where('is_deleted', '0')
                ->exists();

            if ($duplicate) {
                return Redirect::back()
                    ->withInput()
                    ->with('type', 'error')
                    ->with('message', 'This project URL is already in use.');
            }

            DB::transaction(function () use ($request, $user, $project, $projectUrl, $languages) {
                $project->project_url = $projectUrl;
                $project->project_meta_tags = $request->input('project_meta_tags');
                $project->project_meta_description = $request->input('project_meta_description');

                $project->project_class = $request->input('project_class');
                $project->project_ref_no = $request->input('project_ref_no');
                $project->project_link_code = $request->input('project_link_code');
                $project->project_black_tapped = $request->input('project_black_tapped');
                $project->project_gravel = $request->input('project_gravel');
                $project->project_earthen = $request->input('project_earthen');
                $project->project_under_construction = $request->input('project_under_construction');
                $project->project_plan = $request->input('project_plan');

                $project->project_latitude = $request->input('project_latitude');
                $project->project_longitude = $request->input('project_longitude');
                $project->project_zoom = $request->input('project_zoom');
                $project->is_active = $request->input('is_active');
                $project->updated_by = $user->id;
                $project->save();

                foreach ($languages as $language) {
                    $languageId = $language->id;

                    // Match by both project_id and language_id.
                    // Never trust the hidden detail_id submitted by the browser.
                    $detail = DB::table('project_detail')
                        ->where('project_id', $project->id)
                        ->where('language_id', $languageId)
                        ->first();

                    $detailData = [
                        'project_heading' => trim($request->input('project_heading_' . $languageId)),
                        'project_description' => trim($request->input('project_description_' . $languageId)),
                        'project_page_title' => trim((string) $request->input('project_page_title_' . $languageId)),
                        'updated_at' => now(),
                    ];

                    if ($detail) {
                        DB::table('project_detail')
                            ->where('id', $detail->id)
                            ->where('project_id', $project->id)
                            ->where('language_id', $languageId)
                            ->update($detailData);
                    } else {
                        $detailData['project_id'] = $project->id;
                        $detailData['language_id'] = $languageId;
                        $detailData['created_at'] = now();

                        DB::table('project_detail')->insert($detailData);
                    }
                }
            });

            return Redirect::route('project.admin.dashboard')
                ->with('type', 'success')
                ->with('message', 'Project is updated successfully.');
        } catch (Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                throw $e;
            }

            report($e);

            return Redirect::back()
                ->withInput()
                ->with('type', 'error')
                ->with('message', 'Project could not be updated. Please try again.');
        }
    }
}
