<div class="utility-group">
    <ul>
        {{-- <li>
            <button onclick='goTool("http://eproc.dor.gov.np/")' type="button" class="btn"
                data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ \Helper::getNameOftext('eproc', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-users"></i>
                    <span class="btn-title">E-Procument Section</span></span>
            </button>
        </li> --}}
        <li>
            <button onclick='goTool("https://grms.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('grms', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-tachometer-alt"></i>
                    <span class="btn-title">(GRMS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("https://bms.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('bms', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-align-justify"></i>
                    <span class="btn-title">(BMS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("https://ssrn.dor.gov.np/")'
                class="btn" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ \Helper::getNameOftext('ssrn-shortcut', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-road forth"></i>
                    <span class="btn-title">(SSRN)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("https://eis.dor.gov.np/main/public_home")' class="btn"
                data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ \Helper::getNameOftext('eis', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-car"></i>
                    <span class="btn-title">(EIS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("https://armp.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('armp', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-building"></i>
                    <span class="btn-title">(ARMP)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("https://fmis.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('fmis', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-cubes"></i>
                    <span class="btn-title">(FMIS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("https://cms.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('cms', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-flag"></i>
                    <span class="btn-title">(CMS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("https://pmis.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('pmis', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-atom"></i>
                    <span class="btn-title">(PMIS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("https://tls.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('kvtls', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-boxes"></i>
                    <span class="btn-title">(KVTLS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("https://spare.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('sis', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-campground"></i>
                    <span class="btn-title">(SIS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("https://dms.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('dms', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-clipboard-list"></i>
                    <span class="btn-title">(DMS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("http://202.166.197.27/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('ridms', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-coins"></i>
                    <span class="btn-title">(RIDMS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("http://cas.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('cas', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-cogs"></i>
                    <span class="btn-title">(CAS)</span>
                </span>
            </button>
        </li>
        <li>
            <button onclick='goTool("http://rate.dor.gov.np/")' class="btn" data-bs-toggle="tooltip"
                data-bs-placement="top" title="{{ \Helper::getNameOftext('ras', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-cube"></i>
                    <span class="btn-title">(RAS)</span>
                </span>
            </button>
        </li>
        {{-- <li>
            <button onclick="goTool('{{ '/' . $subdomain }}/getdistancecalc')" class="btn"
                data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ \Helper::getNameOftext('distance-calculator', $languageId) }}">
                <span class="rotate-wrap">
                    <i class="fa fa-calculator"></i>
                    <span class="btn-title">Distance Calculator</span>
                </span>
            </button>
        </li> --}}
    </ul>
</div>
