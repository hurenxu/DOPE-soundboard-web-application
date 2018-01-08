var SBC = {};

SBC.loadData = function () {

    $("#listGrid").html("");

    $.getJSON("./api.php", function(data) {

        if (data.length == 0) {
            $("#listGrid").html("<tr><td colspan='5'>No Users</td></tr>");
        } else {
            $.each(data, function (index,value) {

                var str = "";
                str += "<tr><td>"+value.first_name+"</td>";
                str += "<td>"+value.last_name+"</td>";
                str += "<td>"+value.login+"</td>";
                str += "<td>"+value.password+"</td>";


                str += "<td><div class='row'>";

                str += "<div class='col-sm-6'><button type='button' class='btn btn-default' onclick='SBC.editForm(" + JSON.stringify(value) + ")'><span class='glyphicon glyphicon-pencil'></span></button></div>";


                str += "<div class='col-sm-6'><button type='button' class='btn btn-default' onclick='SBC.confirmDelete(" + value.user_id + ");'><span class='glyphicon glyphicon-trash'></span></button></div>";

                str += "</div></td></tr>";

                $(str).appendTo("#listGrid");
            });
        }
    });
}; /* loadData */


SBC.editForm = function editRecord(record) {

    if (!record) {
        // no data so it is a straight add

        // clear the fields
        $("#user_name").val('');
        $("#last_name").val('');
        $("#login").val('');
        $("#password").val('');
        $("#user_id").val('');


        $("#addModalLabel").html("Register");

        $("#actionBtn").html('Register');
        $("#actionBtn").click(function () {
            SBC.doAdd();
        });

    } else {
        // data passed so it is an edit


        $("#first_name").val(record.first_name);
        $("#last_name").val(record.last_name);
        $("#login").val(record.login);
        $("#password").val(record.password);
        $("#user_id").val(record.user_id);


        $("#addModalLabel").html("Edit Record");

        $("#actionBtn").html('Update');
        $("#actionBtn").click(function () {
            SBC.doUpdate();
        });
    }

    // focus the first field
    $('#singupModal').on('shown.bs.modal', function () {
        $('#user_name').focus()
    });

    $('#singupModal').modal('show');

} /* editForm */


SBC.loginForm = function userLogIn(record) {

    $("#addModalLabel").html("Edit Record");

    $("#actionBtn").html('Update');
    $("#actionBtn").click(function () {
        SBC.doUpdate();
    });

    // focus the first field
    $('#signInModal').on('shown.bs.modal', function () {
        $('#loginMail').focus()
    });

    $('#signInModal').modal('show');

} /* loginForm */


SBC.openSignUp = function userSignUp(){

    // clear the fields
    $("#user_name").val('');
    $("#last_name").val('');
    $("#firstName").val('');
    $("#email").val('');
    $("#password").val('');

    $("#actionBtn").click(function () {
        SBC.doAdd();
    });


    // focus the first field
    $('#singupModal').on('shown.bs.modal', function () {
        $('#user_name').focus()
    });

    $('#singupModal').modal('show');

};/* openSignUp */


SBC.openLogIn = function userSignIn(){


    // clear the fields
    $("#loginId").val('');
    $("#loginPassword").val('');

    // $("#actionBtn").click(function () {
    // 	SBC.doAdd();
    // });


    // focus the first field
    $('#signinModal').on('shown.bs.modal', function () {
        $('#loginId').focus()
    });

    $('#signinModal').modal('show');
};/* openSignUp */


SBC.openAddBoard = function addBoard(){
    $('#imgInp').prop('required',true);
    $("#boardFormTitle").val("Add New Board");

    $("#boardTitle").val("");

    $('#setPublicBoard').prop('checked', true);

    $('#boardForm').attr('action', '/ab');
    // $('#soundImgPreview').attr('src', './img/blue_af.png');

    // focus the first field
    $('#addBoardModal').on('shown.bs.modal', function () {
        $('#boardTitle').focus()
    });

    $("#editBoardBtn").val("Add");

    $('#addBoardModal').modal('show');
};/* openAddBoard */

SBC.openEditBoard = function editBoard(isPublic, boardName, boardCover, actionUrl) {

    $('#imgInp').prop('required',false);
    $("#boardName").val(boardName);
    // $('#soundImgPreview').attr('src', './img/blue_af.png');

    // focus the first field
    $('#addBoardModal').on('shown.bs.modal', function () {
        $('#boardName').focus()
    });

    $('#boardForm').attr('action', actionUrl);

    console.log(isPublic);
    if (isPublic == 1) {
        $('#setPublicBoard').prop('checked', true);
    } else {
        $('#setPublicBoard').prop('checked', false);
    }

    $("#boardFormTitle").text("Edit Board");

    $("#editBoardBtn").val("Update");

    $('#addBoardModal').modal('show');
};

SBC.openAddSound = function addSound() {
    $("#soundFormTitle").text("Add New Sound");

    $("#soundName").val("");
    // $('#soundImgPreview').attr('src', './img/blue_af.png');

    $("#editSoundBtn").val("Add");
    // focus the first field
    $('#addSoundModal').on('shown.bs.modal', function () {
        $('#soundName').focus()
    }).modal('show');

    $('#soundForm').attr('action', '/as');

    $('#imgInp').prop('required',true);
    $('#soundFile').prop('required',true);
    // $('#addSoundModal').modal('show');
};/* openAddSound */

SBC.openEditSound = function editSound(soundName, imgPath, soundPath, actionUrl) {
    $("#soundFormTitle").text("Edit Sound");

    $('#imgInp').prop('required',false);
    $('#soundFile').prop('required',false);

    $("#soundName").val(soundName);

    $('#soundForm').attr('action', actionUrl);
    // $('#soundImgPreview').attr('src', './img/blue_af.png');

    $("#editSoundBtn").val("Update");
    // focus the first field
    $('#addSoundModal').on('shown.bs.modal', function () {
        $('#soundName').focus()
    }).modal('show');
};

SBC.openDeleteConfirm = function deleteConfirm(actionUrl) {

    $('#deleteForm').attr('action', actionUrl);
    $('#addDeleteModal').modal('show');
};

SBC.updateUploadImg = function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#boardImgPreview').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}/* updateUploadImg */

SBC.updateBoard =  function updateSingleBoard(public, boardId){

    if(public){
        $("#boardName").val($("#public_board #boardname_"+boardId).html().substr(74));
        $('#boardImgPreview').attr('src', $("#public_board #example_img_"+boardId).attr('src'));
    }
    else{
        $("#boardName").val($("#boardname_"+boardId).html().substr(74));
        $('#boardImgPreview').attr('src', $("#example_img_"+boardId).attr('src'));

    }

    // focus the first field
    $('#addBoardModal').on('shown.bs.modal', function () {
        $('#boardName').focus()
    });

    $("#editBoardBtn").val("Save");

    $('#addBoardModal').modal('show');
}



SBC.doAdd = function doAdd(public, pageNum, mode) {

    var requestContent = "";

    requestContent += public?'/c':'/e';
    requestContent += mode?'g/':'l/';
    requestContent += pageNum

    console.log("Young Rich Nigga");

    jQuery.ajax({
        type: "GET",
        url: requestContent,
        //data: JSON.stringify(obj),
        //contentType: "application/json; charset=utf-8",
        //dataType: "json",
        success: function (data, status, jqXHR) {

            console.log(data);

            //$("#actionBtn").unbind("click");
            $("#public_board").html(data);
            $("#addModal").modal('hide');
            //SBC.loadData();
        },

        error: function (jqXHR, status) {
            // error handler
        }

    });
    console.log("Send Request");
}; /* doAdd */

SBC.addSound = function doAddSound() {
    //addSoundModal

    $("#boardName").val("");
    $('#boardImgPreview').attr('src', './img/blue_af.png');


    // focus the first field
    $('#addSoundModal').on('shown.bs.modal', function () {
        $('#soundName').focus()
    });

    $('#addBoardModal .modal-title').html('Add Sound');

    $("#editBoardBtn").val("Add");

    $('#addBoardModal').modal('show');
}

SBC.modifySound = function doModifySound(soundId) {
    //addSoundModal

    $("#soundName").val($("#soundName_"+soundId).html());

    console.log($(".privateBoardArea #example_img_"+soundId).attr('src'));
    $('#soundImgPreview').attr('src', $(".privateBoardArea #example_img_"+soundId).attr('src'));


    // focus the first field
    $('#addSoundModal').on('shown.bs.modal', function () {
        $('#soundName').focus()
    });

    $('#addSoundModal .modal-title').html('Edit Sound');

    $("#editSoundBtn").val("Save");

    $('#addSoundModal').modal('show');
}



SBC.updateBoard =  function updateSingleBoard(public, boardId){

    if(public){
        $("#boardName").val($("#public_board #boardname_"+boardId).html().substr(74));
        $('#boardImgPreview').attr('src', $("#public_board #example_img_"+boardId).attr('src'));
    }
    else{
        $("#boardName").val($("#boardname_"+boardId).html().substr(74));
        $('#boardImgPreview').attr('src', $("#example_img_"+boardId).attr('src'));

    }

    // focus the first field
    $('#addBoardModal').on('shown.bs.modal', function () {
        $('#boardName').focus()
    });

    $("#editBoardBtn").val("Save");

    $('#addBoardModal').modal('show');
}




SBC.doUpdate = function doUpdate() {

    var obj = {};
    obj.first_name = $("#first_name").val();
    obj.last_name = $("#last_name").val();
    obj.login = $("#login").val();
    obj.password = $("#password").val();
    obj.user_id = $("#user_id").val();



    jQuery.ajax({
        type: "PUT",
        url: "./api.php/"+obj.user_id,
        data: JSON.stringify(obj),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (data, status, jqXHR) {

            $("#actionBtn").unbind("click");
            $("#addModal").modal('hide');
            SBC.loadData();
        },

        error: function (jqXHR, status) {
            // error handler
        }
    });
} /* doUpdate */

// bind load events
$(document).ready(function () {

    $("#signupBtn").click(function () {
        console.log("Clicked Sign Up");
        SBC.openSignUp();
    });

    $("#signinBtn").click(function () {
        console.log("Clicked Sign In");
        SBC.openLogIn();
    });

    $("#public_tab").click(function () {
        console.log("public tab clicked");
    });

    $("#private_tab").click(function () {
        console.log("private tab clicked");
    });

    $("#add_board").click(function () {
        SBC.openAddBoard();
    });

    $(".modifyBtn").click(function () {
        var actionUrl = $(this).attr('url');
        var boardParen = $(this).parent();
        var isPublic = boardParen.next().next().attr('type');
        var boardName = boardParen.prev().children().text();
        var boardCover = boardParen.parent().parent().children('.album_img').prop('src');

        SBC.openEditBoard(isPublic, boardName, boardCover, actionUrl);
    });

    $(".soundModifyBtn").click(function () {
        var actionUrl = $(this).attr('url');
        var soundParent = $(this).parent();
        var soundName = soundParent.prev().children().text();
        var imgPath = soundParent.parent().prev().prop('src');
        var soundPath = soundParent.parent().prev().prev().children().prop('src');
        SBC.openEditSound(soundName, imgPath, soundPath, actionUrl);
    });

    $(".listSoundModifyBtn").click(function () {
        var actionUrl = $(this).attr('url');
        var soundParent = $(this).parent();
        var soundName = soundParent.parent().prev().text();
        var imgPath = soundParent.prev().prop('src');
        var soundPath = soundParent.parent().prev().prev().children().prop('src');
        SBC.openEditSound(soundName, imgPath, soundPath, actionUrl);
    });

    $('.deleteBtn').click(function () {
        var actionUrl = $(this).attr('url');
        SBC.openDeleteConfirm(actionUrl);
    });

    $("#add_sound").click(function () {
        SBC.openAddSound();
    });

    $("#imgInp").change(function(){ SBC.updateUploadImg(this);});

    $("#setSoundBtn_1").click(function () {
        SBC.modifySound(1);
        console.log("???");
    });

    $('.list-play-button').click(function () {
        console.log($(this).next().prop("paused"));
        if ($(this).next().prop("paused") === false) {
            $(this).next().trigger('pause');
        } else {
            console.log('goushi2');
            $(this).next().trigger('play');
        }
    });

    $('.mbtn').click(function () {
        if ($(this).parent().next().prop("paused") === false) {
            $(this).parent().next().trigger('pause');
        } else {
            $(this).parent().next().trigger('play');
        }
    });

    $('.mSound').
    bind('play', function () {
        $(this).prev().children('.mbtn').children('.play').attr('src', '/img/pause.svg');
    }).
    bind('pause', function () {
        $(this).prev().children('.mbtn').children('.play').attr('src', '/img/play.svg');
    });
//$(".setSoundBtn").click(fucntion(){  });

// $("#public_board #setBoardBtn_1").click(function(){ SBC.updateBoard(true, 1);});
// $("#public_board #setBoardBtn_2").click(function(){ SBC.updateBoard(true, 2);});
// $("#public_board #setBoardBtn_3").click(function(){ SBC.updateBoard(true, 3);});

// $("#private_board #setBoardBtn_1").click(function(){ SBC.updateBoard(false, 1);});
// $("#private_board #setBoardBtn_2").click(function(){ SBC.updateBoard(false, 2);});
// $("#private_board #setBoardBtn_3").click(function(){ SBC.updateBoard(false, 3);});

});
