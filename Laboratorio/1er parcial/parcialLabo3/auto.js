"use strict";
var Garcia;
(function (Garcia) {
    class Auto {
        constructor(patente, marca, color, precio) {
            this.patente = patente;
            this.marca = marca;
            this.color = color;
            this.precio = precio;
        }
        toString() {
            return `"patente": "${this.patente}","marca":"${this.marca}","color":"${this.color}","precio": "${this.precio}"`;
        }
        toJSON() {
            return "{" + this.toString() + "}";
        }
    }
    Garcia.Auto = Auto;
})(Garcia || (Garcia = {}));
//# sourceMappingURL=auto.js.map