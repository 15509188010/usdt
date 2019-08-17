<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="nav-close"><i class="fa fa-times-circle"></i> </div>
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
            <span><img alt="image" class="img-circle" src="/Public/Front/img/avatar.jpg"
                       style="width: 64px;height: 64px;"></span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
              <span class="clear">
                  <span class="block m-t-xs">
                      <strong class="font-bold">ADMIN</strong>
                  </span>
              </span>
                    </a>
                    <span style="color:#F30">
                <switch name="member.groupid">
				    <case value="1">总管理员</case>
				</switch></span>
                    <p><a onclick="#" data-toggle="modal" data-target="#myModal"><?=$this->e(date('Y-m-d'))?></a></p>
                </div>
                <div class="logo-element">MENU </div>
            </li>
            <li> <a href="/Manage/User/main?token=<?=$this->e($token)?>"> <i class="fa fa-home"></i> <span class="nav-label">管理首页</span>  </a></li>
            <li> <a href="#"> <i class="fa fa-asterisk"></i> <span class="nav-label">系统设置</span> <span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li> <a href="/Manage/Other/admin?token=<?=$this->e($token)?>" class="J_menuItem">管理列表</a></li>
                    <li> <a href="/Manage/Other/notic?token=<?=$this->e($token)?>" class="J_menuItem">公告列表</a></li>
                </ul>
            </li>
            <li> <a href="#"> <i class="fa fa-user"></i> <span class="nav-label">用户管理</span> <span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href="/Manage/User/center?token=<?=$this->e($token)?>" class="J_menuItem">会员列表</a></li>
                </ul>
            </li>
            <li> <a href="#"> <i class="fa fa-bank"></i> <span class="nav-label">接口管理</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="" class="J_menuItem">APP接口管理</a></li>
                </ul>
            </li>
            <li> <a href="#"> <i class="fa fa fa-sellsy"></i> <span class="nav-label">交易管理</span> <span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href="/Manage/Usdt/into?token=<?=$this->e($token)?>" class="J_menuItem">USDT转入</a> </li>
                    <li><a href="/Manage/Usdt/out?token=<?=$this->e($token)?>" class="J_menuItem">USDT转出</a> </li>
                    <li><a href="/Manage/Usdt/usdtLog?token=<?=$this->e($token)?>" class="J_menuItem">USDT流水</a> </li>
                    <li><a href="/Manage/Usdt/dmrLog?token=<?=$this->e($token)?>" class="J_menuItem">DMR流水</a> </li>
                    <li> <a href="/Manage/Usdt/changeLog?token=<?=$this->e($token)?>" class="J_menuItem">资金变动记录</a> </li>
                    <li> <a href="/Manage/Usdt/frozenLog?token=<?=$this->e($token)?>" class="J_menuItem">解冻记录</a> </li>
                </ul>
            </li>
            <li> <a href="#"> <i class="fa fa fa-cubes"></i> <span class="nav-label">交易设置</span> <span class="fa arrow"></span> </a>
                <ul class="nav nav-second-level">
                    <li><a href="/Manage/Config/setDmr?token=<?=$this->e($token)?>" class="J_menuItem">DMR设置</a> </li>
                    <li><a href="/Manage/Config/setUsdt?token=<?=$this->e($token)?>" class="J_menuItem">USDT设置</a> </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

