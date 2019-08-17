<?php include './App/Views/Public/header.php'; ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-content">
            <form class="layui-form" action="" id="profile">
                <div class="layui-inline">
                    <label class="layui-form-label" style="text-align: left">标题：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="title" lay-verify="required" autocomplete="off" value="" placeholder="标题" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <div class="layui-inline">
                        <label class="layui-form-label" style="text-align: left">公告内容：</label>
                        <div class="layui-input-inline">
                            <textarea id="demo" style="display: none;" name="memo" lay-verify="required"></textarea>
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
                url:"/Manage/Other/saveNotic?token="+token,
                type:"post",
                data:$('#profile').serialize(),
                success:function(res){
                    if(res.code==200){
                        layer.alert("保存成功", {icon: 6},function () {
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
<script>
    layui.use('layedit', function(){
        var layedit = layui.layedit;
        layedit.build('demo'); //建立编辑器
    });
</script>
<!--统计代码，可删除-->
</body>
</html>