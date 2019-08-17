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
                        <label class="layui-form-label">DMR解冻比例：</label>
                        <div class="layui-input-block">
                            <input type="text" name="ratio" value="<?=$this->e($data['ratio'])?>" lay-verify="required" autocomplete="off" placeholder="例如: 3% 在此填写 0.03" class="layui-input">
                        </div>
                    </div>
<!--                    <div class="layui-form-item">-->
<!--                        <label class="layui-form-label">解冻周期：</label>-->
<!--                        <div class="layui-input-block">-->
                          <input type="hidden" name="t" value="--><?//=$this->e($data['t'])?><!--" lay-verify="required" placeholder="解冻周期" autocomplete="off" class="layui-input">
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="layui-form-item">-->
<!--                        <label class="layui-form-label">最小解冻数量：</label>-->
<!--                        <div class="layui-input-block">-->
                            <input type="hidden" name="min" value="<?=$this->e($data['min'])?>" autocomplete="off" class="layui-input" placeholder="例如：500.00">
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="layui-form-item">-->
<!--                        <label class="layui-form-label">最大解冻数量：</label>-->
<!--                        <div class="layui-input-block">-->
                            <input type="hidden" name="max" value="<?=$this->e($data['max'])?>" autocomplete="off"
                                   class="layui-input" placeholder="例如：500.00">
<!--                        </div>-->
<!--                    </div>-->
                    <div class="layui-form-item">
                        <label class="layui-form-label">DMR转出费率：</label>
                        <div class="layui-input-block">
                            <input type="text" name="out_rate" value="<?=$this->e($data['out_rate'])?>" autocomplete="off"
                                   class="layui-input" placeholder="例如: 3% 在此填写 0.03">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">DMR价格设置：</label>
                        <div class="layui-input-block">
                            <input type="text" name="dmr_price" value="<?=$this->e($data['dmr_price'])?>" autocomplete="off"
                                   class="layui-input" placeholder="dmr价格设置">
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
                url:"/Manage/Config/saveDmrConfig?token="+token,
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