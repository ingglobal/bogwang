<div id="dv_<?=$this->id?>">
    <input type="hidden" id="<?=$this->id?>" name="<?=$this->name?>" value="<?=$this->value?>">
    <select id="<?=$this->id1?>" required class="frm_input required bct1">
        <?php if(count($cats1)){ ?>
        <?php foreach($cats1 as $k => $v){ ?>
        <option value="<?=$k?>"<?=(($cats[0] == $k)?' selected':'')?>><?=$v?></option>
        <?php } ?>
        <?php } ?>
    </select>
    <select id="<?=$this->id2?>" required class="frm_input required bct2">
        <?php if(count($cats2)){ ?>
        <?php foreach($cats2 as $k => $v){ ?>
        <option value="<?=$k?>"<?=(($cats[1] == $k)?' selected':'')?>><?=$v?></option>
        <?php } ?>
        <?php } ?>
    </select>
    <select id="<?=$this->id3?>" required class="frm_input required bct3">
        <?php if(count($cats3)){ ?>
        <?php foreach($cats3 as $k => $v){ ?>
        <option value="<?=$k?>"<?=(($cats[2] == $k)?' selected':'')?>><?=$v?></option>
        <?php } ?>
        <?php } ?>
    </select>
    <select id="<?=$this->id4?>" required class="frm_input required bct4">
        <?php if(count($cats4)){ ?>
        <?php foreach($cats4 as $k => $v){ ?>
        <option value="<?=$k?>"<?=(($cats[3] == $k)?' selected':'')?>><?=$v?></option>
        <?php } ?>
        <?php } ?>
    </select>
</div>
<script>
var id = '#<?=$this->id?>';
var id1 = '#<?=$this->id1?>';
var id2 = '#<?=$this->id2?>';
var id3 = '#<?=$this->id3?>';
var id4 = '#<?=$this->id4?>';
var category_list_call_url = '<?=$category_list_call_url?>';
$(id1).on('change',function(){
    //alert($(this).val());
    cat_call($(this).attr('id'),$(this).val());
});

function cat_call(btn,val){
    alert(btn+":"+category_list_call_url);
}
</script>