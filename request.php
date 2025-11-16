<html lang="en"> 
    <head> 
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
        </title>
    </head>
    <body>
        <div style="display:grid;">
            <form style="width:100%">
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">URL</label>
                    <input style="width:100%;margin-block:4px;padding:4px" type="text" id="formurl" name="url">
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">FORM METHOD</label>
                    <select style="width:100%;margin-block:4px;padding:4px" id="formmethod" name="method">
                        <option value="get">get</option>
                        <option value="post">post</option>
                    </select>
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">FORM FIELDS</label>
                    <input style="width:100%;margin-block:4px;padding:4px" type="text" id="formparams"  name="params"> 
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">FORM HEADERS</label>
                    <input style="width:100%;margin-block:4px;padding:4px" type="text" value="Content-Type=application/json" id="formheaders"  name="headers">
                </div>
                <div>
                    <button onclick='request()' style="width:100%;margin-block:4px;padding:4px">TEST</button>
                </div>
            </form>
            <div style="width:100%;overflow:scroll; padding:22px" id="render">
                <pre>
                    <?php

                        if (!isset($_GET['url'])) {
                            echo "missing url"; 
                            exit;
                        }

                        $url = $_GET['url'];
                        $method = $_GET['method'] ?? 'GET';
                        $rawParams = $_GET['params'] ?? '';
                        $headersRaw = $_GET['headers'] ?? '';

                        $params = [];
                        if ($rawParams !== '') {
                            foreach (explode(',', $rawParams) as $param) {
                                list($k,$v) = array_pad(explode('=', $param, 2), 2, '');
                                $params[$k] = $v;
                            }
                        }

                        $headers = [];
                        if ($headersRaw !== '') {
                            foreach (explode(',', $headersRaw) as $hdr) {
                                list($k,$v) = array_pad(explode('=', $hdr, 2), 2, '');
                                $headers[] = "$k: $v";
                            }
                        }

                        $ch = curl_init();

                        // build GET or POST
                        if (strtolower($method) === 'get') {
                            $url .= '?' . http_build_query($params);
                        } else {
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                        }

                        // base setup
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        if (!empty($headers)) {
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        }
                        $resp = curl_exec($ch);
                        if ($resp === false) {
                            echo "CURL ERROR: " . curl_error($ch);
                        } else {
                            echo $resp;
                        }

                        curl_close($ch);
                    ?>
                </pre>
            </div>
        </div>
        <!-- <script> 
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
            const set_headers=(req,headers)=>
            {
                console.info('headers',headers)
                Object.keys(headers).map(
                    k=>{
                        console.info(k,headers[k])
                        req.setRequestHeader(k,headers[k])
                    }
                )
            }
            const custom_req = (url,headers={}) => {
                let _req = new XMLHttpRequest()
                return {
                    post: (data, cb) => {
                        _req.open('POST', url)
                        set_headers(_req,headers)
                        post(_req, data, cb)
                    },
                    get: (data, cb) => {
                        _req.open('GET', url + formparams(data))
                        set_headers(_req,headers)
                        get(_req, cb)
                    },
                    req: _req
                }
            }
            function parse_headers(headers)
            {
                headers_arr = headers.split(",")
                headers = {}
                headers_arr.map(headerraw=>{
                    header = headerraw.split("=")
                    headers[header[0]] = header[1] ?? ''
                })
                return headers
            }
            function elem(selector) 
            {
                return document.querySelector(selector)
            }

            function elems(selector) 
            {
                return document.querySelectorAll(selector)
            }

            function request() 
            {
                const url = elem("#formurl").value ?? null
                const method = elem("#formmethod").value ?? null
                let params = elem("#formparams").value ?? null
                let headers = parse_headers(elem("#formheaders").value ?? "")
                if (params) 
                {
                    params = params.split(',')
                }
                injection(url, params, method,{headers})
            }

            function injection(url, params, method, optionals={}) 
            {
                if (url && method && params && (params.length)) 
                {
                    const headers = optionals.headers ?? {}
                    params = make_params(params, optionals) 
                    console.info('params are:', params) 
                    const req = custom_req(url,headers) 
                    req[method](params, res => {
                        elem("#render").innerHTML = req.req.response
                    })
                }
            }

            function make_params(params, optionals = {}) 
            {
                const parameters = {}
                params.map(param => {
                    let param_arr = [param, '']
                    if (param.match('=')) 
                    {
                        param_arr = param.split('=')
                    } 
                    else 
                    {
                        param_arr[1] = 'test'
                    }
                    let param_name = param_arr[0]
                    let param_val = param_arr[1] 
                    parameters[param_name] = param_val
                }) 
                return parameters
            }
        </script> -->
    </body>
</html>