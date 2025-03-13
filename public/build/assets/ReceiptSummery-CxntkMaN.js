import{A as O}from"./AppLayout-06ovi2Jk.js";import{P as j}from"./Pagination-LBr2dpyI.js";import{T as E,r as g,s as _,c as N,w as k,o as a,a as e,k as y,x as w,f as p,d as o,F as v,g as q,A as B,e as c,t as r,b as F}from"./app-uevufprU.js";import{_ as M}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-C_GraEJX.js";const R={name:"ReceiptSummery",components:{AppLayout:O,Pagination:j},props:{receipt_summeries:Object,records:Object,bills:Object,errors:Object},setup(C){const t=E({id:null,name:null,sh_general:null,sh_year:new Date().getFullYear()}),i=g(""),n=g(!1),l=g(null);function h(){t.reset(),t.post("/receipt/search")}function f(){t.post("/receipt/search")}function u(){n.value=!1,l.value=null}function s(){if(l.value){let d=Object.create({});d.id=l.value,axios.post("/exportRevenue",d,{responseType:"blob"}).then(x=>{console.log(x);var b=document.createElement("a");document.body.appendChild(b),b.style="display: none";var S=new Blob([x.data],{type:x.headers["content-type"]});const m=document.createElement("a");m.href=window.URL.createObjectURL(S),m.download=`revenue${new Date().getTime()}.xlsx`,m.click()})}}return{search_form:t,search:i,doSearch:f,resetSearchForm:h,isOpen:n,period:l,closeModal:u,exportExcel:s}}},V={class:"py-2"},D={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},P={class:"bg-white shadow sm:rounded-t-lg py-2 px-2 md:px-2"},T={class:"grid grid-cols-1 md:grid-cols-2 gap-6 w-full"},A={class:"col-span-1 sm:col-span-1"},U={class:"py-2"},L={class:"mt-1 flex rounded-md shadow-sm"},J={class:"col-span-1 sm:col-span-1"},Y={class:"py-2"},z={class:"mt-1 flex rounded-md shadow-sm"},G={class:"mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex justify-between"},I={class:"flex"},H={class:"flex"},K={key:0,class:"bg-white overflow-auto shadow-xl sm:rounded-lg"},Q={class:"min-w-full divide-y divide-gray-200"},W={class:"bg-white divide-y divide-gray-200"},X={class:"px-4 py-2 whitespace-nowrap"},Z={class:"px-4 py-2 whitespace-nowrap"},$={class:"px-4 py-2 whitespace-nowrap text-left"},ee={key:0},te=["href"],se={key:1},ae={class:"px-4 py-2 whitespace-nowrap text-left"},oe={key:0},ne=["href"],ie={key:1},re={class:"px-4 py-2 whitespace-nowrap text-left"},le={key:0},de=["href"],pe={key:1},ce={class:"px-4 py-2 whitespace-nowrap text-left"},fe={key:0},ue=["href"],xe={key:1},me={class:"px-4 py-2 whitespace-nowrap text-left"},ge={key:0},ye=["href"],he={key:1},be={class:"px-4 py-2 whitespace-nowrap text-left"},_e={key:0},ke=["href"],we={key:1},ve={class:"px-4 py-2 whitespace-nowrap text-left"},qe={key:0},Ce=["href"],Se={key:1},Oe={class:"px-4 py-2 whitespace-nowrap text-left"},je={key:0},Ee=["href"],Ne={key:1},Be={class:"px-4 py-2 whitespace-nowrap text-left"},Fe={key:0},Me=["href"],Re={key:1},Ve={class:"px-4 py-2 whitespace-nowrap text-left"},De={key:0},Pe=["href"],Te={key:1},Ae={class:"px-4 py-2 whitespace-nowrap text-left"},Ue={key:0},Le=["href"],Je={key:1},Ye={class:"px-4 py-2 whitespace-nowrap text-left"},ze={key:0},Ge=["href"],Ie={key:1},He={class:"px-4 py-2 whitespace-nowrap"},Ke={key:0,ref:"isOpen",class:"fixed z-10 inset-0 overflow-y-auto ease-out duration-400"},Qe={class:"flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"},We={class:"inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full",role:"dialog","aria-modal":"true","aria-labelledby":"modal-headline"},Xe={class:"bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"},Ze={class:""},$e={class:"mb-4"},et=["value"],tt={class:"bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"},st={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},at={class:"mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto"},ot={key:1,class:"w-full block mt-4"},nt={class:"text-xs text-gray-600"},it={key:2};function rt(C,t,i,n,l,h){const f=_("pagination"),u=_("app-layout");return a(),N(u,null,{header:k(()=>t[8]||(t[8]=[e("h2",{class:"font-semibold text-xl text-white leading-tight"},"Receipt Summery",-1)])),default:k(()=>[e("div",V,[e("div",D,[e("div",P,[e("div",T,[e("div",A,[e("div",U,[t[10]||(t[10]=e("label",{for:"sh_general",class:"block text-sm font-medium text-gray-700"},"General ",-1)),e("div",L,[t[9]||(t[9]=e("span",{class:"z-10 leading-snug font-normal absolute text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2"},[e("i",{class:"fas fa-user"})],-1)),y(e("input",{type:"text","onUpdate:modelValue":t[0]||(t[0]=s=>n.search_form.sh_general=s),name:"sh_general",id:"sh_general",class:"pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300",placeholder:"Customer/Company Name etc.",tabindex:"1"},null,512),[[w,n.search_form.sh_general]])])])]),e("div",J,[e("div",Y,[t[11]||(t[11]=e("label",{for:"sh_year",class:"block text-sm font-medium text-gray-700"},"Bill Year",-1)),e("div",z,[y(e("input",{type:"text","onUpdate:modelValue":t[1]||(t[1]=s=>n.search_form.sh_year=s),name:"sh_year",id:"sh_year",class:"py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300",tabindex:"2"},null,512),[[w,n.search_form.sh_year]])])])])])]),e("div",G,[e("div",I,[e("a",{onClick:t[2]||(t[2]=(...s)=>n.doSearch&&n.doSearch(...s)),class:"cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},t[12]||(t[12]=[p("Search "),e("i",{class:"ml-1 fa fa-search text-white",tabindex:"9"},null,-1)])),e("a",{onClick:t[3]||(t[3]=(...s)=>n.resetSearchForm&&n.resetSearchForm(...s)),class:"ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition"},t[13]||(t[13]=[p("Reset "),e("i",{class:"ml-1 fa fa-undo-alt text-white",tabindex:"10"},null,-1)]))]),e("div",H,[e("a",{onClick:t[4]||(t[4]=s=>n.isOpen=!0),class:"cursor-pointer inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"},t[14]||(t[14]=[p("Export Excel "),e("i",{class:"ml-1 fa fa-download text-white",tabindex:"11"},null,-1)]))])]),i.receipt_summeries.data?(a(),o("div",K,[e("table",Q,[t[39]||(t[39]=e("thead",{class:"bg-gray-50"},[e("tr",null,[e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," No. "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Customer ID"),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Jan "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Feb "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Mar "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Apr "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," May "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Jun "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Jul "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Aug "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Sep "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Oct "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Nov "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Dec "),e("th",{scope:"col",class:"px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Year ")])],-1)),e("tbody",W,[(a(!0),o(v,null,q(i.receipt_summeries.data,(s,d)=>(a(),o("tr",{key:s.id},[e("td",X,r(d+=i.receipt_summeries.from),1),e("td",Z,r(s.ftth_id),1),e("td",$,[s[0]?(a(),o("span",ee,[e("a",{href:`/pdfpreview2/${s[0]}`,target:"_blank"},t[15]||(t[15]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,te)])):(a(),o("span",se,t[16]||(t[16]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",ae,[s[1]?(a(),o("span",oe,[e("a",{href:`/pdfpreview2/${s[1]}`,target:"_blank"},t[17]||(t[17]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,ne)])):(a(),o("span",ie,t[18]||(t[18]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",re,[s[2]?(a(),o("span",le,[e("a",{href:`/pdfpreview2/${s[2]}`,target:"_blank"},t[19]||(t[19]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,de)])):(a(),o("span",pe,t[20]||(t[20]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",ce,[s[3]?(a(),o("span",fe,[e("a",{href:`/pdfpreview2/${s[3]}`,target:"_blank"},t[21]||(t[21]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,ue)])):(a(),o("span",xe,t[22]||(t[22]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",me,[s[4]?(a(),o("span",ge,[e("a",{href:`/pdfpreview2/${s[4]}`,target:"_blank"},t[23]||(t[23]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,ye)])):(a(),o("span",he,t[24]||(t[24]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",be,[s[5]?(a(),o("span",_e,[e("a",{href:`/pdfpreview2/${s[5]}`,target:"_blank"},t[25]||(t[25]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,ke)])):(a(),o("span",we,t[26]||(t[26]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",ve,[s[6]?(a(),o("span",qe,[e("a",{href:`/pdfpreview2/${s[6]}`,target:"_blank"},t[27]||(t[27]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,Ce)])):(a(),o("span",Se,t[28]||(t[28]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",Oe,[s[7]?(a(),o("span",je,[e("a",{href:`/pdfpreview2/${s[7]}`,target:"_blank"},t[29]||(t[29]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,Ee)])):(a(),o("span",Ne,t[30]||(t[30]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",Be,[s[8]?(a(),o("span",Fe,[e("a",{href:`/pdfpreview2/${s[8]}`,target:"_blank"},t[31]||(t[31]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,Me)])):(a(),o("span",Re,t[32]||(t[32]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",Ve,[s[9]?(a(),o("span",De,[e("a",{href:`/pdfpreview2/${s[9]}`,target:"_blank"},t[33]||(t[33]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,Pe)])):(a(),o("span",Te,t[34]||(t[34]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",Ae,[s[10]?(a(),o("span",Ue,[e("a",{href:`/pdfpreview2/${s[10]}`,target:"_blank"},t[35]||(t[35]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,Le)])):(a(),o("span",Je,t[36]||(t[36]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",Ye,[s[11]?(a(),o("span",ze,[e("a",{href:`/pdfpreview2/${s[11]}`,target:"_blank"},t[37]||(t[37]=[e("i",{class:"fa fas fa-check-square text-green-600"},null,-1)]),8,Ge)])):(a(),o("span",Ie,t[38]||(t[38]=[e("i",{class:"fa fas fa-minus-square text-gray-400"},null,-1)])))]),e("td",He,r(s.year),1)]))),128))])]),n.isOpen?(a(),o("div",Ke,[e("div",Qe,[t[42]||(t[42]=e("div",{class:"fixed inset-0 transition-opacity"},[e("div",{class:"absolute inset-0 bg-gray-500 opacity-75"})],-1)),t[43]||(t[43]=e("span",{class:"hidden sm:inline-block sm:align-middle sm:h-screen"},null,-1)),t[44]||(t[44]=p("​ ")),e("div",We,[e("div",Xe,[e("div",Ze,[e("div",$e,[t[41]||(t[41]=e("label",{for:"period",class:"block text-gray-700 text-sm font-bold mb-2"},"Select Billing :",-1)),y(e("select",{id:"period","onUpdate:modelValue":t[5]||(t[5]=s=>n.period=s),name:"period",class:"py-2.5 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm",tabindex:"2"},[t[40]||(t[40]=e("option",{value:"0"}," -Choose Package-",-1)),(a(!0),o(v,null,q(i.bills,s=>(a(),o("option",{key:s.id,value:s.id},r(s.name),9,et))),128))],512),[[B,n.period]])])])]),e("div",tt,[e("span",st,[e("button",{onClick:t[6]||(t[6]=(...s)=>n.exportExcel&&n.exportExcel(...s)),type:"button",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},"Export")]),e("span",at,[e("button",{onClick:t[7]||(t[7]=(...s)=>n.closeModal&&n.closeModal(...s)),type:"button",class:"inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"},"Cancel")])])])])],512)):c("",!0)])):c("",!0),i.receipt_summeries.total?(a(),o("span",ot,[e("label",nt,r(i.receipt_summeries.data.length)+" Paid Customers in Current Page. Total Number of Paid Customers : "+r(i.receipt_summeries.total),1)])):c("",!0),i.receipt_summeries.links?(a(),o("span",it,[F(f,{class:"mt-6",links:i.receipt_summeries.links},null,8,["links"])])):c("",!0)])])]),_:1})}const ut=M(R,[["render",rt]]);export{ut as default};
