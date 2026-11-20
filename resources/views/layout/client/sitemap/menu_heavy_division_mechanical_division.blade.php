@if (count($HeavydivisionOffices) > 0)
    <li>
        <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
            aria-expanded="false"
            href="#">{{ \Helper::getNameOftext('heavy-equipment-divisions-mechanical-offices', $languageId) }}</a>
        <ul>

            <li>
                <a
                    href="#">{{ \Helper::getNameOftext('heavy-equipment-divisions', $languageId) }}</a>
                <ul>
                    @foreach ($HeavydivisionOffices as $office)
                        <li><a
                                href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <li>
                <a href="#">{{ \Helper::getNameOftext('mechanical-offices', $languageId) }}</a>
                <ul>
                    @foreach ($MechanicalOffices as $office)
                        <li><a
                                href="{{ url($office->office_subdomain) }}">{{ $office->office_name }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>

        </ul>
    </li>
@endif
