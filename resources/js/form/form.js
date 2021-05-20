import Errors from './errors.js'

class Form{
	constructor(data){
		this.formData = data
		for(let field in data){
			this[field]=data[field];
		}

		this.errors = new Errors();
	}

	data(){
		let data = {};
		for(let property in this.formData){	
			data[property] = this[property];
		}
		return data
	}

	submit(requestType,url,file){
		let header =''
		let data = this.data()

		if(file){
			header = {
				headers : {
					'Content-Type' : 'multipart/form-data'
				}
			}

			data = new FormData();
			for (let property in this.formData) {				
				var p =this[property]
				if(Array.isArray(p) && typeof(p[0])==='object'){
					for (var i = 0; i < p.length; i++) {						
						data.append(`${property}[]`,p[i])
					}
				}else{
					data.append(property,p);
				}
			}
		}

		return new Promise((resolve,reject)=>{
			axios[requestType](url,data,header)
			.then(response=>{
				this.onSuccess(response.data)
				resolve(response.data);
			})
			.catch(error => {
				this.onFail(error.response.data);
				reject(error.response.data);
			});
		});
	}

	onSuccess(response){	
		this.reset();
	}


	onFail(errors){
		this.errors.record(errors)
	}

	reset(){
		for(let field in this.formData){
			this[field] = ''
		}
		this.errors.clear();
	}
}

export default Form;