<div
    x-data="{
        open:false,

        search:'',

        selectedCode:'{{ $selected ?? '+60' }}',

        countries:@js(config('countries')),


        init(){

            this.search = this.selectedCode;

        },


        get filteredCountries(){

            if(this.search === ''){

                return this.countries;

            }


            return this.countries.filter(country =>

                country.code
                    .includes(this.search)

            );

        },


        select(country){

            this.selectedCode = country.code;

            this.search = country.code;

            this.open=false;

        },


        formatCode(){

            let value=this.search;


            value=value.replace(/[^0-9+]/g,'');


            if(value !== '' && !value.startsWith('+')){

                value='+'+value;

            }


            this.search=value;

            this.selectedCode=value;

        }

    }"

    class="relative"
>


<input
    type="hidden"
    name="{{ $name ?? 'Country_Code' }}"
    x-model="selectedCode">


<div class="flex">


<span
    class="px-3 py-2 bg-gray-100 border border-r-0 rounded-l-lg">
    +
</span>


<input
    type="text"

    x-model="search"

    @focus="open=true"

    @input="formatCode()"

    class="
    w-full
    border
    rounded-r-lg
    px-3
    py-2
    "

    placeholder="60"
>


</div>



<div

x-show="open"

@click.outside="open=false"

class="
absolute
z-[999]
w-full
bg-white
border
rounded-lg
shadow-lg
mt-1
max-h-60
overflow-y-auto
">


<template x-for="country in filteredCountries"
:key="country.code">


<div

@click="select(country)"

class="
px-3
py-2
hover:bg-gray-100
cursor-pointer
">


<span x-text="country.name"></span>

<span class="text-gray-500">
(<span x-text="country.code"></span>)
</span>


</div>


</template>


</div>


</div>