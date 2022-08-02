<aside class="main-sidebar">
    <aside class="main-sidebar direction">
        <section class="sidebar">

            <div class="user-panel">
                <div class="header" style="padding-left: 10px;padding-right: 10px;">
                    <img src="{{asset('images/logo.png')}}" class="img img-responsive" style="margin: auto;">
                </div>
            </div>

            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{asset('images/default-profile.jpg')}}" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>{{ Auth::user()->name }}</p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>

            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                        <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </form>

            <ul class="sidebar-menu">

                <li class="header">منو اصلی</li>

                <li><a href="#"><i class="glyphicon glyphicon-user"></i><span> Link num.1</span></a></li>

            </ul>

        </section>
    </aside>
</aside>