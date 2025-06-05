<?php
// admin/content/obrazky/editor.php
?>
<form id="blkt-image-form" method="post" enctype="multipart/form-data"
      style="display:flex;flex-direction:column;height:100%;">
  <div style="display:flex;gap:1rem;flex:1;overflow:auto;">
    <!-- vlevo: upload zóna + preview -->
    <div style="flex:1;display:flex;flex-direction:column;">
      <div id="blkt-upload-zone" class="blkt-upload-zone"
           style="flex:1;display:flex;align-items:center;justify-content:center;
                  border:2px dashed #ccc;cursor:pointer;border-radius: 8px">
        <p style="text-align: center; margin-top: 50px">Sem přetáhněte obrázek<br>nebo klikněte</p>
        <input type="file" id="blkt-file-input" name="blkt_file"
               accept="image/*" style="display:none;">
      </div>
      <img id="blkt-preview" src="" alt="Náhled"
           style="display:none;max-width:100%;margin-top:1rem;">
    </div>

    <!-- vpravo: metadata -->
    <div style="flex:1;display:flex;flex-direction:column;padding-top:13px;">
      <input type="hidden" name="blkt_id" id="blkt-image-id" value="">
      <div class="blkt-formular-skupina">
        <input type="text" id="blkt-original-name" name="blkt_original_name"
               readonly placeholder=" ">
        <label for="blkt-original-name">Originální název</label>
      </div>
      <div class="blkt-formular-skupina">
        <input type="text" id="blkt-title" name="blkt_title" placeholder=" ">
        <label for="blkt-title">Titulek</label>
      </div>
      <div class="blkt-formular-skupina">
        <input type="text" id="blkt-alt" name="blkt_alt" placeholder=" ">
        <label for="blkt-alt">Alt text</label>
      </div>
      <div class="blkt-formular-skupina" style="flex:1;display:flex;flex-direction:column;">
        <textarea id="blkt-description" name="blkt_description" placeholder=" "
                  style="flex:1;"></textarea>
        <label for="blkt-description">Popis</label>
      </div>
    </div>
  </div>

  <div style="margin-top:1rem;display:flex;gap:1rem;">
    <button type="button" id="blkt-image-cancel" class="btn btn-cancel">Zrušit</button>
    <button type="submit" class="btn btn-save">Uložit</button>
  </div>
</form>
