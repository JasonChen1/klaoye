class Errors{
	constructor(){
		this.errors={};
	}

	get(field){
		if(this.errors[field]){
			return this.errors[field][0];
		}
	}

	record(ers){
		this.errors = ers;
	}

	has(field){
		return this.errors.hasOwnProperty(field)
	}

	clear(field){
		field ? delete this.errors[field] : this.errors={};
	}

	hasErrors(){
		return Object.keys(this.errors).length > 0;
	}
}

export default Errors;