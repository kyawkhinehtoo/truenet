<template>
<div class="w-full flex justify-between text-sm px-2">
<label class="mt-5 uppercase text-md"> Ticket Overdue Hours Chart</label> 
<button @click="getList()" class="my-2 ml-2 align-right text-center items-center px-3 py-2 bg-indigo-500 border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:border-gray-900 disabled:opacity-25 transition mr-1"> <i class="fas fa-sync opacity-75 text-sm"></i></button>
</div>


  <div v-if="bundle">
  
    <h2 class="m-2 uppercase text-md"></h2>
    <div class="min-w-full divide-y divide-gray-200 mt-1 shadow-xl">
                <div class="bg-gray-50 text-center inline-grid grid-cols-4 gap-2rounded-t-lg w-full">
               
                    <div  class="px-2 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider col-span-1">Ticket ID</div>
                    <div  class="px-2 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider col-span-1">SLA Overdue</div>
                    <div  class="px-2 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider col-span-1">Actual Hrs</div>
                    <div  class="px-2 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider col-span-1">Based  SLA</div>
                  
             
                </div>
                <div class="bg-white rounded-b-lg  divide-y divide-gray-200 text-sm text-center max-h-80 overflow-auto block scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-white w-full">
                  <div v-for="row in bundle" v-bind:key="row.id" class="cursor-pointer inline-grid grid-cols-4 gap-2 w-full" >
                   <div class="px-2 py-2 whitespace-nowrap col-span-1"  @click="doEdit(row)">{{ row.code }}</div>
                   <div class="px-2 py-2 whitespace-nowrap col-span-1">{{getDay(row.over)}}</div>
                   <div class="px-2 py-2 whitespace-nowrap col-span-1"> {{getDay(row.diff)}}</div>
                   <div class="px-2 py-2 whitespace-nowrap col-span-1">{{ row.percentage }}</div>
                    
                  </div>
                </div>
    </div>
  </div>
  

</template>

<script>
import { ref, onMounted, onUpdated } from "vue";
import Label from '@/Jetstream/InputLabel.vue';

export default {
  components: { Label },
  name: "IncidentAlert",
  emits: ["show_edit"],
  setup(props,context) {
    let bundle = ref("Loading ..");
    let remain = ref("Loading ..");
    const getData = async () => {
      let url = "/incidentOverdue";
      console.log(url);
      try {
        const res = await fetch(url);
        const data = await res.json();
        return data;
      } catch (err) {
        console.error(err);
      }
      
    }
    const getRemain = async () => {
      let url = "/incidentRemain";
      console.log(url);
      try {
        const res = await fetch(url);
        const data = await res.json();
        return data;
      } catch (err) {
        console.error(err);
      }
      
    }
    function getDay(data){
        let minutes = (data)%3600/60;
        let hours = (data)%(24*3600)/3600;
        let days = (data)/(24*3600);
        let value = null;
  
        if(data >= 86400){
          let hrs = days - Math.floor(days)
          days = days - hrs
          hrs = hrs * 24
          let min = hrs -  Math.floor(hrs);
          hrs = hrs - min;
          if(hrs != 0){
            value = days+" d, "+ hrs +" hr";
          }else{
            value = days+" d"
          }
          
        }
        else if(data > 1440){
            let min = hours -  Math.floor(hours);
            let hr = hours - min;
             min = Math.round(min * 60);
            if(hr != 0){
              value = hr +" hr," + min +" m" ;
            }else{
               value =  min +" m" ;
            }
            
        }else{
            value = Math.round(minutes)+" m"
        }
        return value;
    }
    function percenttosecond(data){
      let a_month = 2592000;
      let second = a_month - (data/100) * a_month;
      return second;
    }
    function doEdit(row){
      context.emit("show_edit", row);
    }
    function getList(){
        getData().then((d) => {
        bundle.value = d
       });
        getRemain().then((d) => {
        remain.value = d
       });
     
    }
    onMounted(() => {
      getList();
      //let timer = setInterval(getList, 60000);
    });
    return { bundle, remain, getList ,getDay,percenttosecond,doEdit};
  },
};
</script>
<style scoped>
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltip .tooltiptext {
  visibility: hidden;
  min-width: 120px;
  background-color: #4f46e5;
  color: #fff;
  text-align: center;
  border-radius: 5px;
  padding: 5px 0;
  left:-25px;
  top:-3px;
  /* Position the tooltip */
  position: absolute;
  z-index: 1;
  opacity:0.9;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}
</style>