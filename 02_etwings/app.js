var Taker = require('./taker');
var express = require('express');
var app = express();

var round = function(val, digit){
    var w = Math.pow(10, digit);
    return Math.round(w * val) / w;
}
var monaTaker = new Taker('mona_jpy');
var update = function(sec){
    setTimeout(function(){
        monaTaker.update(function(err){
            update(30)
        })
    }, sec * 1000);
}

var getCurrentRate = function(yen, risk_ratio){
    var v = monaTaker.sell(yen);
    var mona = yen / (v[v.length-1][2] * risk_ratio);
    var total_amount = v[v.length-1][2];
    return {
        date:Math.floor(monaTaker.date.getTime()/1000),
        price:round(mona, 2),
        amount:round(total_amount,2),
        total:round(mona*total_amount,2),
    }
}
app.get('/mona_jpy', function (req, res) {
    var risk_ratio = 1.05;
    var yen = 500000;
    var rate = getCurrentRate(yen, risk_ratio);
    res.send(JSON.stringify(rate));
});
app.get('/fullbids', function (req, res) {
    var b = JSON.stringify(monaTaker.fullbids().map(function(v){
        return {price:v[0],amount:v[1],totalamount:v[2],totalprice:v[3]}
    }));
    res.send(b);
});
app.use(express.static('./public_html', {}));

update(0);
app.listen(3000);
