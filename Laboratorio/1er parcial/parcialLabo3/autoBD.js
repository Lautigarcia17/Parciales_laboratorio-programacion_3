"use strict";
/// <reference path="./auto.ts"/>
var Garcia;
(function (Garcia) {
    class AutoBD extends Garcia.Auto {
        constructor(patente, marca, color, precio, path_foto = "") {
            super(patente, marca, color, precio);
            this.path_foto = path_foto;
        }
        toJSON() {
            return `{ ${super.toString()},"foto": "${this.path_foto}"}`;
        }
    }
    Garcia.AutoBD = AutoBD;
})(Garcia || (Garcia = {}));
//# sourceMappingURL=autoBD.js.map