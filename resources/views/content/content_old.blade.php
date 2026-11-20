@extends('layout.client.container')
@section('content')
    <?php
    use App\Models\Home;
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $selectDor = $languageId == 1 ? 'कार्यालय छान्नुहोस्' : 'Select DOR Office';
    ?>

    <div class="container">
        <section class="margin-bottom">
            <div class="breadcrum">
                <ul>
                    <li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
                    <li><i class="fa fa-chevron-right"></i></li>
                    <li>{{ $contentData->content_heading }}</li>
                    <li>{{ $contentData->content_heading }}</li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-3 sidebar">
                    <ul>
                        <?php
                        if (count($childContents) > 0) {
                            echo '<ul class="inside">';

                            foreach ($childContents as $content) {
                                $active = $contentData->id == $content->id ? 'active' : '';
                                echo '<a href="' . $content->content_url . '"><li class="' . $active . '">' . $content->content_heading . '</li></a>';
                            }
                            echo '</ul>';
                            echo '</li>';
                        } else {
                            echo '<ul class="inside">';
                            foreach ($parentContents as $content) {
                                $active = $contentData->id == $content->id ? 'active' : '';
                                echo '<li class="' . $active . '"><a href="' . $content->content_url . '">' . $content->content_heading . '</a></li>';
                            }
                            echo '</ul>';
                            echo '</li>';
                        }
                        ?>
                    </ul>

                </div>
                <div class="col-md-9 wow fadeInRight animated inside_content"
                    style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3 class="hentry__title">{{ $contentData->content_heading }}</h3>

                    <div class="col-md-12 col-sm-12 ">
                        <div class="table-responsive">
                            {!! $contentData->content_description !!}
                            <?php
                            $listLev2Header = Home::headerContentAllList($contentData->id, $contentData->parent_id);
                            // print_r(count($listLev2Header));exit;

                            echo Helper::GetSubContentDoclist($contentData->content_url, $contentData->id, '1');

                            ?>
                        </div>

                    </div>
                </div>
        </section>
    </div>
    <?php

    ?>


@stop
