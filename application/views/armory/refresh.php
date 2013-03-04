<?php echo form_open('/armory/dorefresh/' . $character['name']);?>
    <fieldset>
        <ul>
            <li>Session ID: <input type="text" name="sessid" value=""/> </li>
        </ul>
        <input type="submit" value="Import" />
    </fieldset>
<?php echo form_close();?>