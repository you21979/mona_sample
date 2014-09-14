var etwings = require('etwings');
var api = etwings.PublicApi;

var filterOverSum = function(max){
    var sum = 0;
    var flag = true;
    return function(v){
        if(!flag) return false;
        sum += v[0] * v[1];
        if(sum >= max) flag = false;
        return true;
    }
}
var thenBids = function(){
    return function(v){ return v.bids }
}
var thenAsks = function(){
    return function(v){ return v.asks }
}
var mapOutput = function(){
    return function(v){ return { price : v[0], amount: v[1]} }
}

var bid_by_price = function(pair,price){
    return api.depth(pair).
        then(thenBids()).
        filter(filterOverSum(price))
}
var ask_by_price = function(pair, price){
    return api.depth(pair).
        then(thenAsks()).
        filter(filterOverSum(price))
}

var thenExactPrice = function(price){
    return function(v){
        var sum = 0;
        var result = [];
        for(var i = 0; i<v.length; ++i){
            sum += v[i][0] * v[i][1];
            if(sum >= price){
                var remain = sum - price;
                var total = v[i][0] * v[i][1];
                v[i][1] = v[i][1] - (v[i][1] * (remain/total));
                result.push(v[i]);
                break;
            }else{
                result.push(v[i]);
            }
        }
        return result;
    }
}

var mapTotalAmount = function(){
    var total = 0;
    return function(v){
        total += v[1];
        return [v[0], v[1], total]
    }
}

var sell = exports.sell = function(pair, price){
    return bid_by_price(pair, price).then(thenExactPrice(price)).map(mapTotalAmount())
}

var buy = exports.buy = function(pair, price){
    return ask_by_price(pair, price).then(thenExactPrice(price)).map(mapTotalAmount())
}

