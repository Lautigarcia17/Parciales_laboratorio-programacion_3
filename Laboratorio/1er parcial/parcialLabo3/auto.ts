namespace Garcia
{
    export class Auto
    {
        public  patente :string;
        public  marca :string;
        public color :string;
        public precio :number;

        public constructor(patente : string, marca : string, color : string, precio : number)
        {
            this.patente = patente;
            this.marca = marca;
            this.color = color;
            this.precio = precio;
        }

        public toString() : string
        {
            return `"patente": "${this.patente}","marca":"${this.marca}","color":"${this.color}","precio": "${this.precio}"`;
        }

        public toJSON() : string
        {
            return "{" + this.toString() + "}";
        }

    }
}
