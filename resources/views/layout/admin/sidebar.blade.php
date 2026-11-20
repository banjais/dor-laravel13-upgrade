@php
use App\Models\UserAccessControlModel;
$user = Auth::user();
$roleId = (int) ($user->user_type_id ?? 0);
$is_restricted_admin = ($roleId === 13);
@endphp
<div id="sidebar" class="nav-collapse collapse">

    <div class="sidebar-toggler hidden-phone"></div>
    <ul class="sidebar-menu">
        <li class=" active">
            <a href="{{ url('/admin/home') }}" class="">
                <span class=" icon-box"> <i class="icon-dashboard"></i></span> Dashboard
            </a>
        </li>
        @if ($user && (int) $user->user_type_id === 2)
            <li id="website-theme">
                <a href="{{ route('admin.website-theme.index') }}">
                    <span class="icon-box"><i class="icon-tint"></i></span>
                    Configuration
                </a>
            </li>
            <li id="grms-settings">
                <a href="{{ route('admin.contact-bcc-settings.index') }}">
                    <span class="icon-box"><i class="icon-envelope"></i></span>
                    GRMS
                </a>
            </li>
            <li id="chatbot-settings" class="has-sub {{ request()->routeIs('admin.chatbot-settings.*', 'admin.chatbot-internal-providers.*', 'admin.external-api-settings.*', 'admin.chatbot-knowledge.*') ? 'active' : '' }}">
                <a href="javascript:;" class="">
                    <span class="icon-box"><i class="icon-comments"></i></span>
                    Chatbot
                    <span class="arrow"></span>
                </a>
                <ul class="sub">
                    <li id="chatbot-general">
                        <a href="{{ route('admin.chatbot-settings.index') }}">General</a>
                    </li>
                    <li id="chatbot-internal-ai">
                        <a href="{{ route('admin.chatbot-internal-providers.index') }}">Internal AI</a>
                    </li>
                    <li id="chatbot-external-api">
                        <a href="{{ route('admin.external-api-settings.index') }}">External API</a>
                    </li>
                    <li id="chatbot-knowledge">
                        <a href="{{ route('admin.chatbot-knowledge.index') }}">Knowledge</a>
                    </li>
                </ul>
            </li>

        @endif
        @if (!$is_restricted_admin)
        <?php
        $accessList_Q = UserAccessControlModel::getAccessViewByUserMapId(Auth::User()->id);
        $accessList_Q = $accessList_Q->toArray();

        $accessList = [];
        foreach ($accessList_Q as $key => $item) {
            // dd($item);
            $accessList[] = $item->module_id;
        }

        $officeId = Auth::User()->office_id;
        ?>

        @if ($user && (int) $user->user_type_id === 2)
            <li id="flash-box-settings">
                <a href="{{ route('admin.flash-box-settings.index') }}">
                    <span class="icon-box"><i class="icon-bullhorn"></i></span>
                    Flash Box
                </a>
            </li>
        @endif
        <?php if(in_array(1,$accessList) || in_array(20,$accessList) || in_array(30,$accessList) ) { ?>
        <li id="content" class="has-sub">
            <a href="javascript:;" class="">
                <span class=" icon-box"> <i class="icon-home"></i></span> Content
                <span class="arrow"></span>
            </a>
            <ul class="sub">
                @if (in_array(30, $accessList))
                    <li id="homes"><a class="" href=" {{ route('admin.homes') }}">Homes</a></li>
                @endif
                @if (in_array(1, $accessList))
                    <li id="contents"><a class="" href=" {{ route('admin.contents') }}">Contents</a>
                    </li>
                @endif
                @if (in_array(20, $accessList))
                    <li id="projects"><a class="" href=" {{ route('admin.projects') }}">Projects</a>
                    </li>
                @endif
            </ul>
        </li>
        <?php } ?>

        <?php
					if(in_array(2,$accessList) || in_array(4,$accessList) || in_array(10,$accessList) || in_array(18,$accessList) || in_array(22,$accessList) || in_array(27,$accessList) || in_array(31,$accessList))
				{ ?>
        <li id="informations" class="has-sub">
            <a href="javascript:;" class="">
                <span class=" icon-box"> <i class="icon-bookmark-empty"></i></span> Information
                <span class="arrow"></span>
            </a>
            <ul class="sub">

                @if (in_array(2, $accessList))
                    <li id="news"><a class="" href=" {{ route('admin.news') }}">News</a></li>
                @endif
                @if (in_array(31, $accessList))
                    <li id="newsticker"><a class="" href=" {{ url('admin/news-ticker') }}">News
                            Ticker</a></li>
                @endif

                @if (in_array(4, $accessList))
                    <li id="notices"><a class="" href=" {{ route('admin.notices') }}">Notice</a></li>
                @endif

                {{-- @if (in_array(4, $accessList))
                    <li id="newsletters"><a class=""
                            href=" {{ route('admin.newsletters') }}">NewsLetters</a>
                    </li>
                @endif
                @if (in_array(22, $accessList))
                    <li id="tenders"><a class="" href=" {{ route('admin.tenders') }}">Tender Notice</a>
                    </li>
                @endif
                @if (in_array(10, $accessList))
                    <li id="presses"><a class="" href=" {{ route('admin.presses') }}">Press</a></li>
                @endif --}}

                @if (in_array(18, $accessList))
                    <li id="downloads"><a class="" href=" {{ route('admin.downloads') }}">Downloads</a></li>
                @endif

                <?php /*@if(in_array(23,$accessList))
                                                                                              			<li id="bills"><a class="" href="{{ route('admin.bills') }}">Bills</a></li>
                                                                                            			@endif */
                ?>
                @if (in_array(27, $accessList))
                    <li id="vehicles"><a class="" href=" {{ route('admin.vehicles') }}">Vehicles</a>
                    </li>
                @endif
                @if (in_array(32, $accessList))
                    <li id="guidelines"><a class=""
                            href=" {{ route('admin.guidelines') }}">Manuals</a>
                    </li>
                @endif
            </ul>
        </li>
        <?php } ?>

        <?php  if(in_array(11,$accessList) || in_array(12,$accessList) ) { ?>
        <li id="publication" class="has-sub">
            <a href="javascript:;" class="">
                <span class=" icon-box"> <i class="icon-bookmark-empty"></i></span> Publications
                <span class="arrow"></span>
            </a>
            <ul class="sub">
                @if (in_array(11, $accessList))
                    <li id="categories"><a class=""
                            href=" {{ route('admin.categories') }}">Category</a></li>
                @endif
                @if (in_array(12, $accessList))
                    <li id="publications"><a class="" href="
                            {{ route('admin.publications') }}">Publications</a></li>
                @endif
            </ul>
        </li>
        <?php } ?>

        <?php  if(in_array(16,$accessList) || in_array(17,$accessList) ) { ?>
        <li id="galleries" class="has-sub">
            <a href="javascript:;" class="">
                <span class=" icon-box"> <i class="icon-bookmark-empty"></i></span> Gallery
                <span class="arrow"></span>
            </a>
            <ul class="sub">
                @if (in_array(16, $accessList))
                    <li id="banners"><a class="" href=" {{ route('admin.banners') }}">Banner</a></li>
                @endif
                @if (in_array(17, $accessList))
                    <li id="albums"><a class="" href=" {{ route('admin.albums') }}">Album</a></li>
                @endif
            </ul>
        </li>
        <?php } ?>


        <?php if (($user && in_array((int) $user->user_type_id, [2, 12], true)) || in_array(5, $accessList) || in_array(6, $accessList) || in_array(7, $accessList) || in_array(8, $accessList)) { ?>
        <li id="usersmgmt" class="has-sub">
            <a href="javascript:;" class="">
                <span class=" icon-box"> <i class="icon-user"></i></span> Account
                <span class="arrow"></span>
            </a>
            <ul class="sub">
                @if ($user && in_array((int) $user->user_type_id, [2, 12], true))
                    <li id="circular-admin-accounts">
                        <a href="{{ route('admin.circular-admin.index') }}">Circular Admin</a>
                    </li>
                @endif
                @if ($user && (int) $user->user_type_id === 12)
                    <li id="office-profile">
                        <a href="{{ route('admin.office.edit', $user->office_id) }}">Office Profile</a>
                    </li>
                @endif
                @if (in_array(5, $accessList))
                    <li id="users"><a class="" href=" {{ route('admin.users') }}">Users</a></li>
                @endif
                @if (in_array(6, $accessList))
                    <li id="usertypes"><a class="" href=" {{ route('admin.usertypes') }}">User
                            Types</a></li>
                @endif
                @if ((!$user || (int) $user->user_type_id !== 12) && in_array(7, $accessList))
                    <li id="offices"><a class="" href=" {{ route('admin.offices') }}">Offices</a></li>
                @endif
                @if (in_array(8, $accessList))
                    <li id="accesscontrol"><a href="{{ route('admin.access.control') }}">ACL Group</a></li>
                @endif
            </ul>
        </li>
        <?php } ?>

        <?php  if(in_array(29,$accessList) || in_array(26,$accessList) || in_array(34,$accessList)) { ?>
        <li id="staffsmgmt" class="has-sub">
            <a href="javascript:;" class="">
                <span class=" icon-box"> <i class="icon-user"></i></span> Staffs
                <span class="arrow"></span>
            </a>
            <ul class="sub">
                @if (in_array(29, $accessList))
                    <li id="staffs"><a class="" href=" {{ route('admin.staffs') }}">Staffs</a></li>
                @endif
                @php

                @endphp
                @if (in_array(34, $accessList))
                    <li id="office_chiefs"><a class=""
                            href=" {{ route('admin.office_chiefs') }}">Office Chiefs</a></li>
                @endif
                @if (in_array(26, $accessList) && Auth::User()->user_type_id != 2)
                    {{-- <li id="emailrequest"><a class=""
                            href=" {{ route('admin.email.request') }}">Request
                            Email</a></li> --}}
                @else
                    <li id="loginrequest"><a class=""
                            href=" {{ route('admin.login-credentials.request') }}">Login Credential
                            Requests</a></li>
                @endif
            </ul>
        </li>
        <?php } ?>

        <?php if(in_array(13,$accessList) || in_array(24,$accessList) || in_array(15,$accessList) || in_array(19,$accessList) || in_array(21,$accessList) || in_array(25,$accessList) || in_array(28,$accessList)) { ?>
        <li id="misc" class="has-sub">
            <a href="javascript:;" class="">
                <span class=" icon-box"> <i class="icon-bookmark-empty"></i></span> Misc
                <span class="arrow"></span>
            </a>
            <ul class="sub">
                @if (in_array(13, $accessList))
                    <li id="laws"><a class="" href=" {{ route('admin.laws') }}">Law & Regulation</a>
                    </li>
                @endif
                {{-- @if (in_array(24, $accessList))
                    <li id="trainings"><a class="" href=" {{ route('admin.trainings') }}">Trainings</a>
                    </li>
                @endif --}}
                @if (in_array(28, $accessList))
                    <li id="circulars"><a class="" href=" {{ route('admin.circulars') }}">Circulars</a>
                    </li>
                @endif
                @if (in_array(15, $accessList))
                    <li id="faqs"><a class="" href=" {{ route('admin.faqs') }}">Faqs</a></li>
                @endif
                @if (in_array(19, $accessList))
                    <li id="seos"><a class="" href=" {{ route('admin.seos') }}">Seo</a></li>
                @endif
                @if (in_array(21, $accessList))
                    <li id="equipments"><a class=""
                            href=" {{ route('admin.equipments') }}">Equipments</a>
                    </li>
                @endif
                @if (in_array(25, $accessList))
                    <li id="log"><a class="" href=" {{ route('admin.logs') }}">Activity Log</a></li>
                @endif
                @if (in_array(33, $accessList))
                    <li id="log"><a class="" href=" {{ route('admin.tools') }}">External Tools</a></li>
                @endif
            </ul>
        </li>
        <?php } ?>
        @else
            @if ($roleId === 12)
                <li id="usersmgmt" class="has-sub">
                    <a href="javascript:;">
                        <span class="icon-box"><i class="icon-user"></i></span> Account
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub">
                        <li id="office-profile">
                            <a href="{{ route('admin.office.edit', $user->office_id) }}">Office Profile</a>
                        </li>
                        <li id="circular-admin-accounts">
                            <a href="{{ route('admin.circular-admin.index') }}">Circular Admin</a>
                        </li>
                    </ul>
                </li>
            @endif
            <li id="circulars">
                <a href="{{ route('admin.circulars') }}">
                    <span class="icon-box"><i class="icon-bookmark-empty"></i></span> Circulars
                </a>
            </li>
        @endif
    </ul>
    <!-- END SIDEBAR MENU -->
</div>
