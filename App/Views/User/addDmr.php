<?php include './App/Views/Public/header.php'; ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-content">
            <form class="layui-form" action="" id="profile">
                <input type="hidden" name="uid" value="<?=$this->e($data['uid'])?>">
                <blockquote class="layui-elem-quote" style="font-size:14px;padding;8px;">当前DMR可用：<span class="label label-info"><?=$this->e($data['dmr'])?></span>
                    当前DMR冻结：<span class="label label-info"><?=$this->e($data['d_frozen'])?></span>
                </blockquote>
                <div class="layui-form-item layui-form-text">
                    <div class="layui-inline">
                        <label class="layui-form-label">增加DMR数量：</label>
                        <div class="layui-input-inline">
                            <input name="num" value="" placeholder="输入DMR数量,例如:100.00" class="layui-input">
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit="submit" lay-filter="save">提交保存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include './App/Views/Public/footer.php'; ?>
<script>
    layui.use(['layer', 'form','laydate'], function(){
        var storage=window.localStorage;
        var token=storage.getItem('token');
        var form = layui.form
            ,laydate = layui.laydate
            ,layer = layui.layer;

        //监听提交
        form.on('submit(save)', function(data){
            $.ajax({
                url:"/Manage/User/saveEditDmr?token="+token,
                type:"post",
                data:$('#profile').serialize(),
                success:function(res){
                    if(res.code==200){
                        layer.alert("赠送成功", {icon: 6},function () {
                            parent.location.reload();
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                        });
                    }else{
                        layer.alert("操作失败", {icon: 5},function () {
                            parent.location.reload();
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                        });
                    }
                }
            });
            return false;
        });
    });
</script>
<!--统计代码，可删除-->
</body>
</html>