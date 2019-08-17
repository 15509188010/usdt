<?php include './App/Views/Public/header.php'; ?>
<div class="row">
    <div class="col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>基本设置</h5>
            </div>
            <div class="ibox-content">
                <form class="layui-form" action="" autocomplete="off" id="baseForm">
                    <div class="layui-form-item">
                        <label class="layui-form-label">设置平台USDT地址：</label>
                        <div class="layui-input-block">
                            <input type="text" name="ad_usdt" value="<?=$this->e($data['ad_usdt'])?>" lay-verify="required" autocomplete="off" placeholder="usdyt地址" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">设置USDT转出费率：</label>
                        <div class="layui-input-block">
                            <input type="text" name="usdt_rate" value="<?=$this->e($data['usdt_rate'])?>" lay-verify="required" autocomplete="off" placeholder="usdt转出费率" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="" lay-filter="add">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include './App/Views/Public/footer.php'; ?>
<script>
    layui.use(['form', 'laydate','upload'], function(){
        var layer = layui.layer
            ,form = layui.form
            ,laydate = layui.laydate

            ,upload = layui.upload;

        //普通图片上传
        var uploadInst = upload.render({
            elem: '#test1'
            ,url: '<{:U("System/uploadImg")}>'
            ,before: function(obj){
                //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#demo1').attr('src', result); //图片链接（base64）
                });
            }
            ,done: function(res){
                console.log(res);
                //如果上传失败
                $('#wx_img').val(res['data']);
                return layer.msg(res['msg']);
                //上传成功
            }
            ,error: function(){
                //演示失败状态，并实现重传
                var demoText = $('#demoText');
                demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
                demoText.find('.demo-reload').on('click', function(){
                    uploadInst.upload();
                });
            }
        });

        //监听提交
        form.on('submit(add)', function(data){
            var storage=window.localStorage;
            var token=storage.getItem('token');
            $.ajax({
                url:"/Manage/Config/saveUsdtConfig?token="+token,
                type:"post",
                data:$('#baseForm').serialize(),
                success:function(res){
                    if(res.code==200){
                        layer.alert("操作成功", {icon: 6},function () {
                            location.reload();
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                        });
                    }else{
                        layer.msg("操作失败!", {icon: 5},function () {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                        });
                        return false;
                    }
                }
            });
            return false;
        });
    });
</script>
</body>
</html>