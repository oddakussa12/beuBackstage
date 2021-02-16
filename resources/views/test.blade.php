<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="/js/inline-attachment.js"></script>
<script src="/js/jquery.inline-attachment.js"></script>
<textarea id="editor_id" name="content" >
    HTML内容
</textarea>

@include('kindeditor::editor',['editor'=>'editor_id'])

<script type="text/javascript">
    $(function() {
        $('#editor_id').inlineattachment({
            uploadUrl: 'upload_attachment.php'
        });
    });
</script>