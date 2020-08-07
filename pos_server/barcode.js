var AtalasoftBarcode39Symbols = new Object();
AtalasoftBarcode39Symbols['*'] = "bWbwBwBwb";
AtalasoftBarcode39Symbols['-'] = "bWbwbwBwB";
AtalasoftBarcode39Symbols['$'] = "bWbWbWbwb";
AtalasoftBarcode39Symbols['%'] = "bwbWbWbWb";
AtalasoftBarcode39Symbols[' '] = "bWBwbwBwb";
AtalasoftBarcode39Symbols['.'] = "BWbwbwBwb";
AtalasoftBarcode39Symbols['/'] = "bWbWbwbWb";
AtalasoftBarcode39Symbols['+'] = "bWbwbWbWb";
AtalasoftBarcode39Symbols['0'] = "bwbWBwBwb";
AtalasoftBarcode39Symbols['1'] = "BwbWbwbwB";
AtalasoftBarcode39Symbols['2'] = "bwBWbwbwB";
AtalasoftBarcode39Symbols['3'] = "BwBWbwbwb";
AtalasoftBarcode39Symbols['4'] = "bwbWBwbwB";
AtalasoftBarcode39Symbols['5'] = "BwbWBwbwb";
AtalasoftBarcode39Symbols['6'] = "bwBWBwbwb";
AtalasoftBarcode39Symbols['7'] = "bwbWbwBwB";
AtalasoftBarcode39Symbols['8'] = "BwbWbwBwb";
AtalasoftBarcode39Symbols['9'] = "bwBWbwBwb";
AtalasoftBarcode39Symbols['A'] = "BwbwbWbwB";
AtalasoftBarcode39Symbols['B'] = "bwBwbWbwB";
AtalasoftBarcode39Symbols['C'] = "BwBwbWbwb";
AtalasoftBarcode39Symbols['D'] = "bwbwBWbwB";
AtalasoftBarcode39Symbols['E'] = "BwbwBWbwb";
AtalasoftBarcode39Symbols['F'] = "bwBwBWbwb";
AtalasoftBarcode39Symbols['G'] = "bwbwbWBwB";
AtalasoftBarcode39Symbols['H'] = "BwbwbWBwb";
AtalasoftBarcode39Symbols['I'] = "bwBwbWBwb";
AtalasoftBarcode39Symbols['J'] = "bwbwBWBwb";
AtalasoftBarcode39Symbols['K'] = "BwbwbwbWB";
AtalasoftBarcode39Symbols['L'] = "bwBwbwbWB";
AtalasoftBarcode39Symbols['M'] = "BwBwbwbWb";
AtalasoftBarcode39Symbols['N'] = "bwbwBwbWB";
AtalasoftBarcode39Symbols['O'] = "BwbwBwbWb";
AtalasoftBarcode39Symbols['P'] = "bwBwBwbWb";
AtalasoftBarcode39Symbols['Q'] = "bwbwbwBWB";
AtalasoftBarcode39Symbols['R'] = "BwbwbwBWb";
AtalasoftBarcode39Symbols['S'] = "bwBwbwBWb";
AtalasoftBarcode39Symbols['T'] = "bwbwBwBWb";
AtalasoftBarcode39Symbols['U'] = "BWbwbwbwB";
AtalasoftBarcode39Symbols['V'] = "bWBwbwbwB";
AtalasoftBarcode39Symbols['W'] = "BWBwbwbwb";
AtalasoftBarcode39Symbols['X'] = "bWbwBwbwB";
AtalasoftBarcode39Symbols['Y'] = "BWbwBwbwb";
AtalasoftBarcode39Symbols['Z'] = "bWBwBwbwb";

function AtalasoftBarcode39(s)
{
    this._bcData = s;
}

AtalasoftBarcode39.prototype.getBCSymbol = function(sym, nw, ww) {
    var w = nw;
    var clr = "black";
    if (sym == "B") {
        w = ww;
    }
    else if (sym == "b") {
        // defaults good
    }
    else if (sym == "W") {
        w = ww;
        clr = "white";
    }
    else if (sym == "w") {
        clr = "white";
    }
    return "<td style='width:"+w+"px;background-color:"+clr+"'></td>";
}

AtalasoftBarcode39.prototype.getBC39 = function(c, nw, ww) {
    var bcData = AtalasoftBarcode39Symbols[c];    
    if (bcData == null) {        
        return "<td>[Illegal Char: "+c+"]</td>";
    }
    var retSymbol = "";
    for (var i = 0; i < bcData.length; ++i) {
        var bar = bcData.substring(i, i+1);        
        retSymbol += this.getBCSymbol(bar, nw, ww);
    }
    return retSymbol;
}

AtalasoftBarcode39.prototype.getBarcode = function(h, nw, ww) {
    var retBarcode = "";
    if (h == null) { 
        h = 40;
    }
    if (nw == null) { 
        nw = 3;
    }
    if (ww == null) { 
        ww = 6;
    }
    retBarcode += "<table cellpadding=0 cellspacing=0 style='height:"+h+"px'><tr style='height:"+h+"px'>";
    retBarcode += this.getBC39("*", nw, ww);
    retBarcode += this.getBCSymbol("w", nw, ww);
    for (var i = 0; i < this._bcData.length; ++i) {        
        retBarcode += this.getBC39(this._bcData.substring(i, i+1), nw, ww);
        retBarcode += this.getBCSymbol("w", nw, ww);
    }
    retBarcode += this.getBC39("*", nw, ww);
    retBarcode += "</tr></table>";
    return retBarcode;
}

function writeBC(s)
{
    var bc = new AtalasoftBarcode39(s);
    return bc.getBarcode(30, 2, 8);
}


AtalasoftBarcode39.prototype.write = function(h, nw, ww) {
    document.write(this.getBarcode(h, nw, ww))
}
