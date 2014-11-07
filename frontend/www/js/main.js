$(document).ready(function(){

    // Set up stuff
    $.support.cors = true;
    $.mobile.allowCrossDomainPages = true;
    app.initialize();
    
    // Initialize listview and table
    $("#products").listview();
    $("#categories").listview();
    $("#cart").listview();
    $("#productInfo").table();

    // Fill categories right away
    $.getJSON(app.serverUrl, {a: "getAll", what: "category"})
        .done(function(data){
            $.each(data, function(i, item){
                $("#categoryDivider").before("<li id='category-"+item.id+"'><a href='#'>"+item.name+"</a></li>");
            });
            $("#categories").listview("refresh");
        });

    // Check user token right away
    User.checkToken(setUserStatus);

    // If cart has items, update cart button
    if(Cart.size() > 0){
        $(".cartButton").html(Cart.size());
    }
    
    // ---------------------------------------- || ----------------------------------------
    // Functions
    // ---------------------------------------- || ----------------------------------------

    function setUserStatus(loggedIn){
        if(loggedIn){
            var user = User.get();
            $("#loginPageButton").attr("href", "#userPage").html(user.username);
            $(".token").val(user.token);
            $(".currentUserUsername").html(user.username);
        } else{
            User.unset();
        }
    }

    // Show loading spinner
    function showSpinner(){
        $.mobile.loading("show");
    }

    // Hide loading spinner
    function hideSpinner(){
        $.mobile.loading("hide");
    }

    // Fill product info table with data from array
    function fillProductInfo(info){
        $("#productInfo > thead:last > tr:last, #productInfo > tbody:last > tr:last").html("");
        $.each(info, function(i, item){
            $("#productInfo > thead:last > tr:last").append("<td>"+item.field+"</td>");
            $("#productInfo > tbody:last > tr:last").append("<td>"+item.value+"</td>");
        });
        $("#productInfo").table("rebuild");
    }


    // ---------------------------------------- || ----------------------------------------
    // jQuery events
    // ---------------------------------------- || ----------------------------------------

    $("#basketAddButton").click(function(){
        var id = parseInt($("#productId").val());
        $("#amount").trigger("change");
        var amount = parseInt($("#amount").val());
        var stock = parseInt($("#productStock").html());
        if(amount > stock){
            app.showAlert("Ikke nok i lagerbeholdning!", "Feil");
            return;
        }
        Cart.add(id, amount);
        $(".cartButton").html(Cart.size());
    });

    // Log the user out
    $("#logoutButton").click(function(){
        $.post(app.serverUrl, {a: User.logoutAction, sessionToken: User.getToken});
        $("#loginPageButton").attr("href", "#loginPage").html("Logg inn");
        User.unset();
        $.mobile.changePage($("#mainPage"), "slide");
    });

    // Fill products list with products from chosen category
    $("#categories").on("click", "li[id^='category-']", function(){
        var id = $(this).attr("id").split("-")[1];
        $(".categoryName").html($("a", this).html());
        showSpinner();
        $.getJSON(app.serverUrl, {a: "getAll", what: "plants", category: id, simple: "true", token: User.getToken})
            .done(function(data){
                $("#products").html("");
                $.each(data, function(i, item){
                    $("#products").append("<li id='product-"+item.id+"'><a href='#'>"+item.name+"</a></li>");
                });
                $("#products").listview("refresh");
                hideSpinner();
                $.mobile.changePage($("#productListPage"), "slide");
            });
    });

    // Fill product information on the product page for chosen product
    $("#products").on("click", "li[id^='product-']", function(){
        var productId = $(this).attr("id").split("-")[1];
        showSpinner()
        $.getJSON(app.serverUrl, {a: "get", what: "plant", id: productId, token: User.getToken})
            .done(function(data){
                $("#productId").val(data.id);
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

    $(".cartButton").click(function(){
        if(Cart.size() > 0){
            showSpinner();
            $.getJSON(app.serverUrl, {a: "getMultiple", what: "plants", ids: Cart.json(), token: User.getToken})
                .done(function(data){
                    $("#cart").html("");
                    var price = 0;
                    $.each(data, function(i, item){
                        var id = parseInt(item.id);
                        var amount = Cart.get()[id];
                        price += parseInt(item.price) * amount;
                        if(item.stock === 0){
                            Cart.remove(id);
                            return true;
                        } else if(amount > item.stock){
                            amount = item.stock;
                        }
                        $("#cart").append("<li id='product-"+item.id+"'>" +
                            "<img src='"+item.imageUrlS+"' />" +
                            "<div style='width: 50%;' class='floatLeft'>" +
                                "<h3>"+item.name+" (<span class='itemPrice'>"+item.price+"</span>,-)</h3>" +
                                "<p>"+item.description+"</p>" +
                            "</div>" +
                            "<table style='width:300px' class='floatRight'><tr>" +
                                "<td>" +
                                    "<input type='number' class='newAmount' min='1' max='"+item.stock+"' value='"+amount+"' />" +
                                "</td>" +
                                "<td style='font-size:80%; width:100px'>" +
                                    "<a href='#' data-role='button' data-icon='recycle' class='amountUpdate'>Oppdater</a>" +
                                "</td>" +
                                "<td style='font-size:80%; width:100px'>" +
                                    "<a href='#' data-role='button' data-icon='delete' class='amountDelete'>Slett</a>" +
                                "</td>" +
                            "</tr></table>" +
                        "</li>");
                    });
                    $("#price").html(price);
                    $("#cart").listview("refresh");
                    hideSpinner();
                    $.mobile.changePage($("#cartPage"), "slide");
                })
        }
    });

    $("#purchaseButton").click(function(){
        showSpinner();
        User.checkToken(function(loggedIn){
            hideSpinner();
            if(!loggedIn){
                $.mobile.changePage($("#loginPage"), "slide");
                return;
            }
            Cart.purchase();
            $.mobile.changePage($("#mainPage"), "slide");
            $(".cartButton").html("Tom");
            app.showAlert("Kjøp komplett!", "Suksess");
        });
    });

    $("#cart").on("click", ".amountUpdate", function(){
        var li = $(this).closest("li");
        var id = parseInt(li.attr("id").split("-")[1]);
        var oldAmount = Cart.get()[id];
        var newAmount = li.find(".newAmount").val();
        var amountDiff = newAmount - oldAmount;
        var currentPrice = parseInt($("#price").html());
        var itemPrice = parseInt(li.find(".itemPrice").html());
        var newPrice = currentPrice + (itemPrice * amountDiff);
        $("#price").html(newPrice);
        Cart.set(id, newAmount);
    });

    $("#cart").on("click", ".amountDelete", function(){
        var li = $(this).closest("li");
        var id = parseInt(li.attr("id").split("-")[1]);
        var oldAmount = Cart.get()[id];
        var newAmount = 0;
        var amountDiff = newAmount - oldAmount;
        var currentPrice = parseInt($("#price").html());
        var itemPrice = parseInt(li.find(".itemPrice").html());
        var newPrice = currentPrice + (itemPrice * amountDiff);
        $("#price").html(newPrice);
        Cart.delete(id);
        li.remove();
        $("#cart").listview("refresh");
        if(Cart.size() === 0){
            $.mobile.changePage($("#mainPage"), "slide");
            $(".cartButton").html("Tom");
        } else{
            $(".cartButton").html(Cart.size());
        }
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

    // Check login info, log in if correct
    $("#loginForm").submit(function(e){
        showSpinner()
        $.post(app.serverUrl, $(this).serialize())
            .done(function(data){
                if(data !== 0){
                    User.set(data.username, data.token, data.access);
                    user = User.get();
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