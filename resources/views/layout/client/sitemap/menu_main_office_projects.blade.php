<li>
    <a class="nav-link dropdown-toggle" id="navbarDropdown_2" role="button" data-bs-toggle="dropdown" aria-expanded="false"
        href="#">{{ \Helper::getNameOftext('projects', $languageId) }}</a>
    <ul>
        <li>
            <a href="#">{{ \Helper::getNameOftext('projects_1', $languageId) }}</a>
            <ul>
                @foreach ($MainProjectOffices_1 as $office)
                    <li><a
                            href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>
                    </li>
                @endforeach
            </ul>
        </li>
        <li>
            <a href="#">{{ \Helper::getNameOftext('projects_2', $languageId) }}</a>
            <ul>
                @foreach ($MainProjectOffices_2 as $office)
                    <li><a
                            href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>
                    </li>
                @endforeach
            </ul>
        </li>
        <li>
            <a href="{{ $abs_site_url . $subdomain . '/projects' }}">
                {{ \Helper::getNameOftext('other-projects', $languageId) }}</a>
        </li>
    </ul>
</li>
