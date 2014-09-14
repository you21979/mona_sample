var etwings = require('etwings');
var api = etwings.PublicApi;

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
    var totalamount = 0;
    var totalprice = 0;
    return function(v){
        totalamount += v[1];
        totalprice += v[0] * v[1];
        return [v[0], v[1], totalamount, totalprice]
    }
}
var Taker = module.exports = function(pair){
    this.result = {}
    this.pair = pair;
    this.date = new Date();
}
Taker.prototype.getUnixTime = function(){
    return Math.floor(this.date.getTime() / 1000);
}
Taker.prototype.update = function(callback){
    var self = this;
    api.depth(this.pair).then(function(v){
        self.date = new Date();
        self.result = v;
        callback(null);
    }).catch(function(e){ callback(e) })
}
Taker.prototype.sell = function(price){
    var then = thenExactPrice(price);
    return then(this.result.bids).map(mapTotalAmount());
}
Taker.prototype.buy = function(price){
    return then(this.result.asks).map(mapTotalAmount());
}
Taker.prototype.fullbids = function(){
    return this.result.bids.map(mapTotalAmount());
}

