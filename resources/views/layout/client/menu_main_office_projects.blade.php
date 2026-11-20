<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" id="navbarDropdown_2" role="button" data-bs-toggle="dropdown" aria-expanded="false"
        href="#">{{ \Helper::getNameOftext('projects', $languageId) }}</a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdown_2">
        <li class="has-sub">
            <a class="dropdown-item" href="#">{{ \Helper::getNameOftext('projects_1', $languageId) }}</a>
            <ul class="submenu dropdown-menu" aria-labelledby="navbarDropdown_2">
                @foreach ($MainProjectOffices_1 as $office)
                    <li><a class="dropdown-item"
                            href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>
                    </li>
                @endforeach
            </ul>
        </li>
        <li class="has-sub">
            <a class="dropdown-item" href="#">{{ \Helper::getNameOftext('projects_2', $languageId) }}</a>
            <ul class="submenu dropdown-menu" aria-labelledby="navbarDropdown_2">
                @foreach ($MainProjectOffices_2 as $office)
                    <li><a class="dropdown-item"
                            href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>
                    </li>
                @endforeach
            </ul>
        </li>
        <li>
            <a class="dropdown-item" href="{{ $abs_site_url . $subdomain . '/projects' }}">
                {{ \Helper::getNameOftext('other-projects', $languageId) }}</a>
        </li>
    </ul>
</li>
