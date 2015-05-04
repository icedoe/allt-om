<?php
    if(isset($id)){
        $save ="Redigera";
        $saveUrl ="comment/edit";
        $delete ="Ta bort";
        $deleteUrl ="comment/remove-id";
    }else{
        $save = "Sänd";
        $saveUrl ="comment/add";
        $delete = "Ta bort alla";
        $deleteUrl = "comment/remove-all";
    }
?>

<div class='comment-form'>
    <form method=post>
        <input type=hidden name="redirect" value="<?=$this->url->create($redirect)?>">
            <fieldset>
            <legend>Lämna en kommentar</legend>
            <p><label>Kommentar:<br/><textarea name='content'><?=$content?></textarea></label></p>
            <p><label>Namn:<br/><input type='text' name='name' value='<?=$name?>'/></label></p>
            <p><label>Hemsida:<br/><input type='text' name='web' value='<?=$web?>'/></label></p>
            <p><label>Epost:<br/><input type='text' name='mail' value='<?=$mail?>'/></label></p>
            <p class=buttons>
                <input type ='hidden' name ='id' value='<?=$id?>' />
                <input type='hidden' name='commenter' value='<?=$commenter?>' />
                <input type='submit' name='doCreate' value='<?=$save?>' onClick="this.form.action ='<?=$this->url->create($saveUrl)?>'" />
                <input type='reset' value='Ångra'/>
                <input type='submit' name='doRemoveAll' value='<?=$delete?>' onClick="this.form.action ='<?=$this->url->create($deleteUrl)?>'"/>
            </p>
            <output><?=$output?></output>
            </fieldset>
    </form>
</div>
