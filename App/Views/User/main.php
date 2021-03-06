<?php include './App/Views/Public/header.php'; ?>
<div class="row">
    <div class="col-sm-12">

        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-warning"></i>欢迎您登陆：
            <strong style="color:#036"><?=$this->e($data['user'])?></strong><span style="color:#F30">
        </span>
        </div>

    </div>
</div>

<div class="row zuy-nav">

    <div class="col-sm-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?=$this->e($data['money'])?></h5>
            </div>
            <div class="ibox-content" style="padding-top:0;height: 67px">
                <h1 class="no-margins">总获佣:</h1>
                <i class="iconfont icon-cunqianguan" style="color: #fff1f3;"></i>
            </div>
        </div>
    </div>


    <div class="col-sm-4" >
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?=$this->e($data['u_money'])?></h5>
            </div>
            <div class="ibox-content" style="padding-top:0;height: 67px">
                <h1 class="no-margins">USDT获得</h1>
                <i class="iconfont icon-tuiguangzhuanqian" style="color: #fffbe8;"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-4" >
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?=$this->e($data['d_money'])?></h5>
            </div>
            <div class="ibox-content" style="padding-top:0;height: 67px">
                <h1 class="no-margins">DMR获得</h1>
                <i class="iconfont icon-iconfontjikediancanicon20" style="color: #f0faf8;"></i>
            </div>
        </div>
    </div>

</div>
<!-- 全局js -->
<?php include './App/Views/Public/footer.php'; ?>
<script src="/Public/Front/js/echarts.common.min.js"></script>
<script>
    layui.use(['laypage', 'layer', 'form'], function () {
        var form = layui.form,
            layer = layui.layer,
            $ = layui.jquery;
    });
    function reset_pwd(title,url,w,h){
        x_admin_show(title,url,w,h);
    }
</script>

</body>
</html>
