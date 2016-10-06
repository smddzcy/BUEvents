<?php
const BASEURL = "http://localhost/BUEvents/view/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <link rel="icon" type="image/png" href="<?php echo BASEURL; ?>assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>

    <title>BU Events</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <meta name="viewport" content="width=device-width"/>
    <link href="<?php echo BASEURL; ?>assets/css/cards.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <link href="<?php echo BASEURL; ?>assets/css/template.css" rel="stylesheet"/>
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link
        href='http://fonts.googleapis.com/css?family=Playfair+Display|Raleway:700,100,400|Roboto:400,700|Playfair+Display+SC:400,700'
        rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>

</head>
<body>

<div id="navbar">
    <nav class="navbar navbar-default" role="navigation" style="padding-top: 3px;height: 70px;margin-bottom: 0;">

        <div class="container-fluid">

            <div class="navbar-header">

                <button type="button" class="navbar-toggle" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1">

                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><i class="fa fa-bullhorn" style="margin-right: 15px;"></i>BU Events</a>
            </div>


            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="http://www.smddzcy.com/contact/" target="_blank">Contact</a></li>
                    <li>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fsmddzcy.com%2FBUEvents%2F"
                           target="_blank" class="btn btn-simple popup"><i class="fa fa-facebook"></i></a>
                    </li>
                    <li>
                        <a href="https://twitter.com/intent/tweet?text=BU%20Events%20-%20All%20Bo%C4%9Fazi%C3%A7i%20events.%20In%20one%20place.&url=http%3A%2F%2Fsmddzcy.com%2FBUEvents%2F"
                           target="_blank" class="btn btn-simple popup"><i class="fa fa-twitter"></i></a>
                    </li>
                    <li>
                        <a href="https://plus.google.com/share?url=http%3A%2F%2Fsmddzcy.com%2FBUEvents%2F"
                           target="_blank" class="btn btn-simple popup"><i class="fa fa-google-plus"></i></a>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</div><!--  end navbar -->

<!-- end navbar  -->
<div class="wrapper" style="background-color: #e9edef; padding-top: 10px;">

    <div class="container" style="padding: 20px 0;">

        <!-- search -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="optSearch">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center" id="headerText"
                 style="padding-bottom: 10px">
                <h5 style="opacity: 0.8;">All events. In one place.</h5>
            </div>
            <div class="col-xs-3 hidden-sm hidden-md hidden-lg" id="optSearchText" style="padding-left: 15px">
                <h6 style="margin-top:22px">Search</h6>
            </div>
            <div class="col-xs-9 col-sm-12 col-md-12 col-lg-12" id="optSearchArea">
                <div id="custom-search-input">
                    <div class="input-group col-md-12">
                        <input type="text" class="form-control input-lg" placeholder="Search for a specific event"
                               style="background-color: #fff" id="searchField"/>
                        <span class="input-group-btn">
                            <button class="btn btn-info btn-lg" type="button" id="search">
                                <i class="glyphicon glyphicon-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-mg-12 col-lg-12"><br/></div>
        <!-- end search -->

        <!-- filter -->
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3" id="optFilter" style="margin-bottom: 20px">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="padding-left: 15px">
                <h6 style="margin-top:22px">Filter</h6>
            </div>
            <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                <button type="button" class="btn btn-lg btn-fill" id="showFilters"
                        style="margin-top:5px; padding: 10px 20px;">Show Filters <span class="fa fa-caret-down"></span></button>
                <button type="button" class="btn btn-lg btn-fill hidden" id="hideFilters"
                style="margin-top:5px; padding: 10px 20px;">Hide Filters <span class="fa fa-caret-up"></span></button>
            </div>
        </div>
        <!-- end filter -->

        <!-- show -->
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3" id="optShow" style="margin-bottom: 20px">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="padding-left: 15px">
                <h6 style="margin-top:22px">Show</h6>
            </div>
            <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                <ul class="nav nav-pills nav-pills-blue">
                    <!--  color-classes: "nav-pills-primary", "nav-pills-info", "nav-pills-warning", "nav-pills-danger", "nav-pills-success" -->
                    <li class="active"><a href="#">Grid</a></li>
                    <li><a href="#" onclick="alert('Will be added soon.')">List</a></li>
                    <li><a href="#" onclick="alert('Will be added soon.')">Map</a></li>
                </ul>
            </div>
        </div>
        <!-- end show -->

        <!-- filter box -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 form-inline hidden" id="filterbox"
             style="margin-bottom: 15px;">

            <div class="text-center underline"><h6>Filter events by</h6></div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <div class="text-center" style="margin-bottom:5px">
                    <small>Date</small>
                </div>
                <div class="row">
                    <div class="col-xs-1 hidden-sm hidden-md hidden-lg"></div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <label for="start_time" style="padding-top:7px"><h6>FROM:</h6></label>
                    </div>
                    <div class="col-xs-6 col-sm-9 col-md-9 col-lg-9">
                        <input class="datepicker form-control" type="text" id="start_time" placeholder="Today" date-format="DD/MM/YYYY"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-1 hidden-sm hidden-md hidden-lg"></div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <label for="end_time" style="padding-top:7px"><h6>TO: </h6></label>
                    </div>
                    <div class="col-xs-6 col-sm-9 col-md-9 col-lg-9">
                        <input class="datepicker form-control" type="text" id="end_time" placeholder="30/12/2100" date-format="DD/MM/YYYY"/>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 hidden-sm hidden-md hidden-lg"><br/></div>
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <div class="text-center" style="margin-bottom:5px">
                    <small>Club</small>
                </div>
                <div class="row" id="allClubs">
                    <div class="hidden-xs col-sm-12 col-md-12 col-lg-12" style="margin-top:5%">
                    </div>
                    <div class="dropdown text-center">
                        <button class="btn dropdown-toggle" type="button" id="clubs">
                            <span id="clubsText"></span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="clubs">
                            <li>
                                <label class="all checkbox" for="checkbox1" style="margin-left: 5%">
                                    <input class="club" type="checkbox" value="All clubs" id="checkbox1"
                                           data-toggle="checkbox" checked>All clubs</label>
                            </li>
                            <li role="presentation" class="divider"></li>
                            <li>
                                <!--<label class="checkbox" for="checkbox2" style="margin-left: 5%"><input type="checkbox" value="compec" id="checkbox2" data-toggle="checkbox">Compec</label>-->
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <div class="text-center" style="margin-bottom:5px">
                    <small>Category</small>
                </div>
                <div class="row" id="allCategories">
                    <div class="hidden-xs col-sm-12 col-md-12 col-lg-12" style="margin-top:5%">
                    </div>
                    <div class="dropdown text-center">
                        <button class="btn disabled" type="button" id="categories"
                                onclick="alert('Will be added soon.')">
                            <!-- disabled for now, add: dropdown-toggle class -->
                            <span id="categoriesText"></span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu"> <!-- add: aria-labelledby="categories" to work -->
                            <li>
                                <label class="all checkbox" for="checkbox2" style="margin-left: 5%">
                                    <input class="category" type="checkbox" value="All categories" id="checkbox2"
                                           data-toggle="checkbox" checked>All categories</label>
                            </li>
                            <li role="presentation" class="divider"></li>
                            <li>
                                <!--<label class="checkbox" for="checkbox1" style="margin-left: 5%"><input type="checkbox" value="wow" id="checkbox4" data-toggle="checkbox">wow</label>-->
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center" style="margin-top: 15px">
                <button type="button" class="btn btn-lg btn-fill" id="filter" style="margin-top:5px">Search</button>
            </div>
        </div>
        <!-- end filter box -->

    </div>

    <div id="events" style="display: inline-block; padding-top: 20px; background-color: #fff; width: 100%;"></div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #fff; margin-top: -6px;">
        <a id="more-events" onclick="moreEvents()">
            <div class="well well-md text-center">
                <h3>More Events &nbsp;<i class="fa fa-arrow-circle-down"></i></h3>
                <!-- a fucking credit to @onrcskn  -->
            </div>
        </a>
    </div>
    <div class="footer register-footer text-center" style="clear:left; margin-top: -6px;">
        <h6 style="background-color: #fff;padding-bottom: 5px;margin-top: 0;">&copy; 2016, BU Events - <a
                href="http://www.smddzcy.com/">smddzcy</a>
        </h6>
    </div>

</div>
<div><h6 class="title title-modern text-center" style="line-height:2; overflow-wrap:break-word;" id="notSupported"></h6>
</div>

<div id="modals">
</div>


</body>

<script src="//code.jquery.com/jquery.min.js"></script>
<script src="<?php echo BASEURL; ?>assets/js/jquery-ui-1.10.4.custom.min.js" type="text/javascript"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" crossorigin="anonymous"></script>

<!--  Plugins -->
<script src="<?php echo BASEURL; ?>assets/js/checkbox.js"></script>
<script src="<?php echo BASEURL; ?>assets/js/bootstrap-select.js"></script>
<script src="<?php echo BASEURL; ?>assets/js/bootstrap-datepicker.js"></script>
<script src="<?php echo BASEURL; ?>assets/js/template.js"></script>
<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDAe64UN6rxbgDo8hzspyTofIGXBiNcE_U"></script>
<script src="<?php echo BASEURL; ?>assets/js/process.js"></script>
</html>
