// Settings
var serverUrl = "http://192.168.10.20/Helseflora/backend/";
var user = null;
var storage;

$(document).ready(function(){

    // Do stuff right away
    $.support.cors = true;
    $.mobile.allowCrossDomainPages = true;
    app.initialize();
    storage = window.localStorage;
    checkLoggedIn();
    $("#products").listview();
    $("#categories").listview();
    $("#productInfo").table();

    // Fill categories right away in background
    $.getJSON(serverUrl, {a: "getAll", what: "category"})
        .done(function(data){
            $.each(data, function(i, item){
                $("#categoryDivider").before("<li id='category-"+item.id+"'><a href='#'>"+item.name+"</a></li>");
            });
            $("#categories").listview("refresh");
        });

    function checkLoggedIn(){
        var item = storage.getItem("user");
        if(item === null){
            unsetUser();
            return;
        }
        user = JSON.parse(item);
        $.post(serverUrl, {a: "checkToken", sessionToken: user.token})
            .done(function(data){
                if(data === 1){
                    $("#loginPageButton").attr("href", "#userPage").html(user.username);
                    $(".token").val(user.token);
                    $(".currentUserUsername").html(user.username);
                } else{
                    unsetUser()
                }
            });
    }

    function showSpinner(){
        $.mobile.loading("show");
    }

    function hideSpinner(){
        $.mobile.loading("hide");
    }

    function unsetUser(){
        $("#loginPageButton").attr("href", "#loginPage").html("Logg inn");
        localStorage.removeItem("user");
        user = null;
    }

    function fillProductInfo(info){
        $.each(info, function(i, item){
            $("#productInfo > thead:last > tr:last").append("<td>"+item.field+"</td>");
            $("#productInfo > tbody:last > tr:last").append("<td>"+item.value+"</td>");
        });
        $("#productInfo").table("rebuild");
    }

    $("#logoutButton").click(function(){
        $.post(serverUrl, {a: "logOut", sessionToken: user.token});
        unsetUser();
        $.mobile.changePage($("#mainPage"), "slide");
    });

    $("#categories").on("click", "li[id^='category-']", function(){
        var id = $(this).attr("id").split("-")[1];
        $(".categoryName").html($("a", this).html());
        showSpinner();
        $.getJSON(serverUrl, {a: "getAll", what: "plants", category: id, simple: "true"})
            .done(function(data){
                $.each(data, function(i, item){
                    $("#products").append("<li id='product-"+item.id+"'><a href='#'>"+item.name+"</a></li>");
                });
                $("#products").listview("refresh");
                hideSpinner();
                $.mobile.changePage($("#productListPage"), "slide");
            });
    });

    $("#products").on("click", "li[id^='product-']", function(){
        var productId = $(this).attr("id").split("-")[1];
        showSpinner()
        $.getJSON(serverUrl, {a: "get", what: "plant", id: productId})
            .done(function(data){
                $("#productName").html(data.name);
                $("#productImgSmall").attr("src", data.imageUrlS);
                $("#productImgLarge").attr("src", data.imageUrlL);
                $("#productStock").html(data.stock);
                $("#productPrice").html(data.price);
                $("#productDescription").html(data.description);
                var info = [
                    {field: "Høyde", value: data.height},
                    {field: "Sone", value: data.zone},
                    {field: "Min. Temp. (Dag)", value: data.min_temp_day},
                    {field: "Min. Temp. (Natt)", value: data.min_temp_night},
                    {field: "Nitrogen", value: data.nitrogen},
                    {field: "Potassium", value: data.potassium},
                    {field: "Phosphorus", value: data.phosphorus}
                ];
                fillProductInfo(info);
                $("#amount").attr("max", data.stock);
                if(data.stock === 0){
                    $("#amount").attr("min", 0);
                    $("#amount").val(0);
                    $("#basketAddButton").addClass("ui-disabled");
                } else{
                    $("#amount").attr("min", 1);
                    $("#amount").val(1);
                    $("#basketAddButton").removeClass("ui-disabled");
                }
                hideSpinner();
                $.mobile.changePage($("#productPage"), "slide");
            });
    });

    // Correct amount field if too much or too little
    $("#amount").change(function(){
        var val = $(this).val();
        var min = $(this).attr("min");
        var max = $(this).attr("max");
        if(val > max){
            $(this).val(max);
        } else if(val < min){
            $(this).val(min);
        }
    });

    $("#loginForm").submit(function(e){
        showSpinner()
        $.post(serverUrl, $(this).serialize())
            .done(function(data){
                if(data !== 0){
                    user = {"username": data.username, "token": data.token, "access": data.access};
                    storage.setItem("user", JSON.stringify(user));
                    alert(user.username);
                    $(".token").val(user.token);
                    $(".currentUserUsername").html(user.username);
                    $("#loginPageButton").attr("href", "#userPage").html(user.username);
                    hideSpinner();
                    $.mobile.changePage($("#mainPage"), "slide");
                } else{
                    hideSpinner();
                    app.showAlert("Feil innloggingsinformasjon", "Feil");
                }
            });
        return false;
    });
});