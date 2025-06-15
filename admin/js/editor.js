var editor;
var contentData = null;
var contentJson = null;

$(window).on('load', function() {

try {
    contentData = JSON.parse(contentJson);
} catch (e) {
    if(contentJson != null){
    contentData = {
        blocks: [
            {
                id: "",
                type: "paragraph",
                data: {
                    text: contentJson
                }
            }
        ]
        };
    }   
}

    editor = new EditorJS({
        holder: "editorJs",
        tools: {
            header: {
                class: Header
            },
            list: {
                class: EditorjsList,
                config: {
                    defaultStyle: 'unordered'
                  }
            },
            embed: {
                class: Embed,
                inlineToolbar: true                
            },
            raw: RawTool
        },
        data: contentData ?? contentData
    });

document.getElementById("submitButton").addEventListener('click',function(e){
    var Form = document.getElementById('editorForm');
    if (Form.checkValidity() == false) {
        var list = Form.querySelectorAll(':invalid');
        for (var item of list) {
            item.focus();
            item.classList.add("error");
        }
        return;
    }
    e.preventDefault();
    editor.save().then((outputData) => {
            $('#editorContent').val(JSON.stringify(outputData));
            $("#editorForm").submit();
        });
    });
});