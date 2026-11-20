@php
use App\Models\Content;
use App\Models\Office;
@endphp
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
        aria-expanded="false">
        {{ $content->content_heading }}
    </a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
        @foreach ($subMenus as $subContent)
            @php
                $childMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $subContent->id, 50);
            @endphp
            @if (count($childMenus) > 0)
                <li class="has-sub"><a class="dropdown-item" href="#">
                        {{ $subContent->content_heading }}</a>
                    <ul class="submenu dropdown-menu">
                        @foreach ($childMenus as $child)
                            @php
                                // dd($child);
                                $subchildMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $child->id, 50);
                            @endphp
                            <li><a class="dropdown-item" href="{{ '/' . $child->content_url }}">
                                    {{ $child->content_heading }}</a></li>
                        @endforeach
                    </ul>
                @else
                <li><a class="dropdown-item"
                        href="{{ $abs_site_url . $subdomain . '/page/' . $subContent->content_url }}">
                        {{ $subContent->content_heading }}</a></li>
            @endif
        @endforeach

    </ul>
</li>
