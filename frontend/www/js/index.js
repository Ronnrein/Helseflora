/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
var app = {
    // Application Constructor
    initialize: function() {
        this.bindEvents();
    },
    // Bind Event Listeners
    //
    // Bind any events that are required on startup. Common events are:
    // 'load', 'deviceready', 'offline', and 'online'.
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    // deviceready Event Handler
    //
    // The scope of 'this' is the event. In order to call the 'receivedEvent'
    // function, we must explicitly call 'app.receivedEvent(...);'
    onDeviceReady: function() {
        app.receivedEvent('deviceready');
    },
    // Update DOM on a Received Event
    receivedEvent: function(id) {
        var parentElement = document.getElementById(id);
        var listeningElement = parentElement.querySelector('.listening');
        var receivedElement = parentElement.querySelector('.received');

        listeningElement.setAttribute('style', 'display:none;');
        receivedElement.setAttribute('style', 'display:block;');

        console.log('Received Event: ' + id);
    },

    showAlert: function(message, title){
        if(navigator.notification){
            navigator.notification.alert(message, null, title, "OK");
        } else{
            alert(title ? (title + ": "+message) : message);
        }
    },

    serverUrl: "http://192.168.10.20/Helseflora/backend/"
};

// ---------------------------------------- || ----------------------------------------
// Objects
// ---------------------------------------- || ----------------------------------------

// User object
var User = {
    storageKey: "user",
    tokenCheckAction: "checkToken",
    logoutAction: "logOut",
    get: function(){
        var item = window.localStorage.getItem(this.storageKey);
        return item !== null ? JSON.parse(item) : null;
    },
    getToken: function(){
        var user = User.get();
        return user === null ? "" : user.token;
    },
    checkToken: function(callback){
        var user = this.get();
        if(user === null){
            if(typeof callback === "function"){
                callback(false);
            }
            return false
        }
        $.post(app.serverUrl, {a: this.tokenCheckAction, sessionToken: user.token})
            .done(function(data){
                var result = data === 1 ? true : false;
                if(typeof callback === "function"){
                    callback(result);
                }
                return result;
            });
    },
    set: function(username, token, access){
        window.localStorage.setItem(this.storageKey, JSON.stringify({"username": username, "token": token, "access": access}));
    },
    unset: function(){
        window.localStorage.removeItem(this.storageKey);
    }
}

// Cart object
var Cart = {
    storageKey: "cart",
    purchaseAction: "purchase",
    get: function(){
        var item = window.localStorage.getItem(this.storageKey);
        return item !== null ? JSON.parse(item) : null;
    },
    add: function(id, amount){
        var items = this.get();
        if(items === null){
            items = {};
        }
        if(id in items){
            items[id] += amount;
        } else{
            items[id] = amount;
        }
        this.save(items);
    },
    remove: function(id, amount){
        var items = this.get();
        if(items === null || !(id in items)){
            return;
        }
        if(items[id] <= amount){
            delete items[id];
        } else{
            items[id] -= amount;
        }
        this.save(items);
    },
    delete: function(id){
        var items = this.get();
        if(items !== null && (id in items)){
            delete items[id];
        }
        this.save(items);
    },
    clear: function(){
        window.localStorage.removeItem(this.storageKey);
    },
    set: function(id, amount){
        var items = this.get();
        if(items === null){
            items = {};
        }
        items[id] = amount;
        this.save(items);
    },
    save: function(items){
        window.localStorage.setItem(this.storageKey, JSON.stringify(items));
    },
    size: function(){
        var cart = this.get();
        return cart !== null ? Object.keys(cart).length : 0;
    },
    json: function(){
        var str = "{";
        var arr = this.get();
        for(var key in arr){
            str += "\""+key+"\": "+arr[key]+",";
        }
        str = str.substring(0, str.length-1)+"}";
        return str;
    },
    purchase: function(){
        $.post(app.serverUrl, {a: this.purchaseAction, items: this.json(), token: User.getToken()})
            .done(function(data){
                console.log(data);
            }).error(function(data){
                console.log(data);
            });
        this.clear();
    }
}
