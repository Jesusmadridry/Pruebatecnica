$(document).ready( () => {

    if(isRegiser()){
        addCompanies();
        $('#register').on('click', () => {
            let username = $('#username').val()
            let password = $('#password').val()
            let cpassword = $('#Cpassword').val()
            let company = $('#companies').find('option:selected').val()
            const URL_POST='http://localhost/fusepong/ProjectsProgress/server/functions.php'
            if($.trim(username) != "" && $.trim(password) != "" && $.trim(cpassword) != ""){
                if(password === cpassword){
                $.post(
                    URL_POST,
                    {
                        data: {
                            functionname: 'createUser',
                            username: username,
                            password: password,
                            company_id: company,
                        }
                    },
                    function (data, status) {
                        console.log(data)
                        let data2 = JSON.parse(data)
                            let { result, response } = data2
                            if (!result){
                                alert(response)
                            }
                            console.log('result', result, 'response', response)
                    })
                }
            }
        })
    }
    

    $('#login').on('click', () => {
        const URL_POST='http://localhost/fusepong/ProjectsProgress/server/functions.php'
        console.log("function login")
        let username = $('#username').val()
        let password = $('#password').val()
        if($.trim(username) != "" && $.trim(password) != ""){
            console.log("entró")
            $.post(
                URL_POST,
                {
                data: {
                    functionname: 'verifyLogin',
                    username: username,
                    password: password,
                }
                },
                function (data, status) {
                    let data2 = JSON.parse(data);
                    let { result, response } = data2;
                    alert(response)
                    if (!result){
                        alert(response)
                    }else {
                        const URL_DASHBOARD = `http://localhost/fusepong/ProjectsProgress/frontend/dashboard.html?username=${username}`
                        localStorage.setItem('username', username)
                        window.open(URL_DASHBOARD);
                    }
                }
            )
        } else {
            console.log("no entró")
        }
    })

    $('#save_project').on('click', () => {
        const URL_POST='http://localhost/fusepong/ProjectsProgress/server/functions.php'
        console.log("save project")
        let name = $('#project_name').val()
        let params = (new URL(document.location)).searchParams;
        let username = params.get('username'); 
        if($.trim(username) != ""){
            $.post(
                URL_POST,{
                    data: {
                        functionname: 'getUser',
                        username: username,
                    }
                }, function(data, status){
                    console.log(data)
                    data = JSON.parse(data)
                    let {result, user} = data

                    if(result){
                        $.post(
                            URL_POST,
                            {
                            data: {
                                functionname: 'createProject',
                                name: name,
                                cid: user.cpn_id,
                                username: user.username,
                            }
                            },
                            function (data, status) {
                                console.log(data)
                                let data2 = JSON.parse(data)
                                let { result, response } = data2
                                if (result){
                                    alert(response)
                                }
                                console.log('result', result)
                            }
                        )
                    }
                }
            )
        }
    })


    if(isDashboard()){
        let params = (new URL(document.location)).searchParams;
        let name = params.get('username'); 
        const URL_POST='http://localhost/fusepong/ProjectsProgress/server/functions.php'
        $('#user_name').text(name)

        $.post(
            URL_POST,
            {
                data: {
                    functionname: 'getUser',
                    username: name,
                }
            },
            function (data, status) {
                let userInfo = JSON.parse(data);
                let { result, user } = userInfo;

                console.log('result', result, 'user', user)
                if(result){
                    let company_id=user.cpn_id
                    $.post(
                        URL_POST,
                        {
                            data: {
                                functionname: 'getProjects',
                                username: user.username,
                                cid: company_id,
                            }
                        },
                        function (data, status) {
                            console.log(data)
                            let projects = JSON.parse(data);
                            let { result, list } = projects;
                            console.log(data)
            
                            console.log('result', result, 'list', list)
                            if(result && list.length > 0){
                                console.log("here")
                                const URL_TICKETS = `http://localhost/fusepong/ProjectsProgress/frontend/tickets.html?pid=`
                                list.forEach((item) => {
                                    $('#projects_table > tbody:last-child').append(
                                        `<tr>
                                            <td>${item.id}</td>
                                            <td><a href =\"${URL_TICKETS}${item.id}\" >${item.name}</a></td>
                                            <td>${item.cpn}</td>
                                            <td>${user.username}</td>
                                        </tr>`);
                                })
                            }
                        }
                    )
                }
            }
        )
    }

    if(isTickets()){
        let params = (new URL(document.location)).searchParams;
        let pid = params.get('pid'); 
        const URL_POST='http://localhost/fusepong/ProjectsProgress/server/functions.php'
        $('#pid').text(pid)
        console.log('pid ',pid)
        
        addTickets(pid);

        $('#create_ticket').on('click', () => {
            const pname = localStorage.getItem('pname')
            $('#ticket_project_name').text(pname)
        })

        $('#Sticket').on('click', () => {
            console.log("save ticket")
            let comments = $('#ticket_comments').val()
            console.log(comments)
            if($.trim(comments) != ""){
                $.post(
                    URL_POST,
                    {
                    data: {
                        functionname: 'createTicket',
                        pid: pid,
                        comments: comments,
                    }
                    },
                    function (data, status) {
                        console.log(data)
                        let data2 = JSON.parse(data)
                        let { result, response } = data2
                        if (result){
                            alert(response)
                        }
                    }
                )
            }
        })
        
        $('#back_tickets').on('click', () => {
            const dashboardUser = localStorage.getItem('username')
            window.location.href = `http://localhost/fusepong/ProjectsProgress/frontend/dashboard.html?username=${dashboardUser}`;
        })

        $('#ticket_table').on('click', (e) => {
            const element = e.target
            console.log('clickedddd')
            // console.log(td.closest('tr'))
            if($(element).is( ":button" )){
                let row = element.closest('tr');
                let column = row.getElementsByTagName("td")[0];
                let tid = column.getElementsByTagName("a")[0].textContent;
                if(confirm ('Are you sure you want to delete this ticket?')) {
                    console.log('delete')
                    $.post(
                        URL_POST,
                        {
                        data: {
                            functionname: 'cancelTicket',
                            tid: tid,
                        }
                        },
                        function (data, status) {
                            console.log(data)
                            data = JSON.parse(data)
                            let { result, response } = data
                            if (result){
                                alert(response)
                            }
                        }
                    )
                }
            }
        })
    }

    if(isDetails()){
        let params = (new URL(document.location)).searchParams;
        let tid = params.get('tid'); 
        const URL_POST='http://localhost/fusepong/ProjectsProgress/server/functions.php';
        $.post(
            URL_POST,
            {
            data: {
                functionname: 'getTicket',
                tid: tid,
            }
            },
            function (data, status) {
                console.log(data)
                data = JSON.parse(data)
                let { result, ticket } = data
                if (result){
                    $('#ticket_comments').val(ticket.comments)
                    $(`#ticket_status option[value='${ticket.status}']`).attr("selected", true);
                    localStorage.setItem('pid', ticket.pid)
                }
                console.log('result', result)
            }
        )

        $('#editTicket').on('click', () => { 
            const comments = $('#ticket_comments').val();
            const status = $('#ticket_status').find('option:selected').val()

            if($.trim(comments) != '' && $.trim(status) != ''){
                $.post(
                    URL_POST,
                    {
                        data: {
                            functionname: 'editTicket',
                            tid: tid,
                            comments: comments,
                            status: status
                        }
                    },
                    function (data, status) {
                        console.log(data)
                        data = JSON.parse(data)
                        let { result, response } = data
                        if (result){
                            alert(response)
                        }
                        console.log('result', result)
                    }
                )
            } else {
                alert('Currently its not possible save. Fill all fields and try again.')
            }
        })

        $('#back_details').on('click', () => {
            const ticketPid= localStorage.getItem('pid')
            window.location.href = `http://localhost/fusepong/ProjectsProgress/frontend/tickets.html?pid=${ticketPid}`;
        })

    }
})


const isDashboard = () => {
    const URL = window.location.href;
    console.log(URL)
    if(URL.includes('dashboard.html')){
        console.log("inside dashboard")
        return true
    } 
    return false
    
}

const isTickets = () => {
    const URL = window.location.href;
    console.log(URL)
    if(URL.includes('tickets.html')){
        console.log("inside tickets")
        return true
    } 
    return false
}

const isRegiser = () => {
    const URL = window.location.href;
    console.log(URL)
    if(URL.includes('register.html')){
        console.log("inside register")
        return true
    } 
    return false
}

const isDetails = () => {
    const URL = window.location.href;
    console.log(URL)
    if(URL.includes('details.html')){
        console.log("inside details")
        return true
    } 
    return false
}

const addCompanies = () => {
    const URL_POST='http://localhost/fusepong/ProjectsProgress/server/functions.php'
    $.post(
        URL_POST,
        {
        data: {
            functionname: 'getCompanies',
        }
        },
        function (data, status) {
            let companies = JSON.parse(data);
            let { result, list } = companies;

            console.log('result', result, 'list', list)
            if(result){
            console.log('result true')
            $('#companies').empty();
            list.forEach((item) => {
                $('#companies').append($('<option>', { 
                    value: item.id,
                    text : item.cpn_name 
                }));
            
            })
            }
        }
    )
}

const addTickets = (pid) => {
    const URL_POST='http://localhost/fusepong/ProjectsProgress/server/functions.php'
    $.post(
        URL_POST,
        {
            data: {
                functionname: 'getTickets',
                pid
            }
        },
        function (data, status) {   
            let tickets = JSON.parse(data);
            let { result, list } = tickets;
            console.log(data)

            console.log('result-ticket', result, 'list', list)
            if(result && list.length > 0){
                const URL_DETAILS = 'http://localhost/fusepong/ProjectsProgress/frontend/details.html?tid=';
                
                console.log('pname', list[0].pname)
                localStorage.setItem('pname', list[0].pname)
                
                $('#ticket_table > tbody').remove();
                list.forEach((item) => {
                    $('#ticket_table').append(
                        `<tr>
                            <td title="Press to Ticket Details"><a href =\"${URL_DETAILS}${item.id}\" target=\"_blank\">${item.id}</a></td>
                            <td>${item.status}</td>
                            <td>${item.pname}</td>
                            <td>${item.developer}</td>
                            <td><button type="button" class="btn btn-danger"> X </button></td>
                        </tr>`);
                })
            }
        }
    )
}