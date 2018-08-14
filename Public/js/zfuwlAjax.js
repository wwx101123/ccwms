/**
 * 修改指定表数据
 */
function updateSort(table, idName, idValue, field, obj) {
    var value = $(obj).val();
    $.ajax({
        type: 'post',
        url: "/index.php?m=Zfuwl&c=Index&a=changeTableVal",
        data: {table: table, idName: idName, idValue: idValue, field: field, value: value},
        success: function (data) {
            if (data.status != 1) {
                layer.msg('更新失败!', {icon: 2});
            } else {
                layer.msg('更新成功', {icon: 1});
            }
        }
    });
}
/**
 * 修改指定表数据
 */
function updateSort2(table, idName, idValue, field, obj) {
    var value = $(obj).val();
    $.ajax({
        type: 'post',
        url: "/index.php?m=Zfuwl&c=Index&a=changeTableVal2",
        data: {table: table, idName: idName, idValue: idValue, field: field, value: value},
        success: function (data) {
            if (data.status != 1) {
                layer.msg('更新失败!', {icon: 2});
            } else {
                layer.msg('更新成功', {icon: 1});
            }
        }
    });
}