@if (count($HeavydivisionOffices) > 0)
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
            aria-expanded="false"
            href="#">{{ \Helper::getNameOftext('heavy-equipment-divisions-mechanical-offices', $languageId) }}</a>
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

            <li class="has-sub">
                <a class="dropdown-item"
                    href="#">{{ \Helper::getNameOftext('heavy-equipment-divisions', $languageId) }}</a>
                <ul class="submenu dropdown-menu" aria-labelledby="navbarDropdown">
                    @foreach ($HeavydivisionOffices as $office)
                        <li><a class="dropdown-item"
                                href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <li class="has-sub">
                <a class="dropdown-item" href="#">{{ \Helper::getNameOftext('mechanical-offices', $languageId) }}</a>
                <ul class="submenu dropdown-menu" aria-labelledby="navbarDropdown">
                    @foreach ($MechanicalOffices as $office)
                        <li><a class="dropdown-item"
                                href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>

        </ul>
    </li>
@endif
