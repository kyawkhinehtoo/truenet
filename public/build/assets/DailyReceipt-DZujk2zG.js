import{A as h}from"./AppLayout-06ovi2Jk.js";import{P as w}from"./Pagination-LBr2dpyI.js";import{r as k,T as v,s as p,c as R,w as y,o as n,a as e,k as V,x as D,b as u,d as r,e as m,t as a,f as b,F as C,g as S}from"./app-uevufprU.js";import{V as j}from"./main-DifJU5vS.js";import{M}from"./vue3-multiselect.umd.min-ChMt84xL.js";import{_ as B}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-C_GraEJX.js";const N={name:"DailyReceipt",components:{AppLayout:h,VueDatePicker:j,Multiselect:M,Pagination:w},props:{receipt_records:Object,bill_list:Object,yesterday_collection:Object,today_collection:Object,select_total:Object,errors:Object},setup(_){const t=k({date:"YYYY-MM-DD",month:"MMM"}),o=v({general:null,date:null,bill_id:null});function l(){o.general=null,o.date=null,o.bill_id=null}const f=()=>{o.post("/dailyreceipt/show",{onSuccess:i=>{},onError:i=>{console.log(i)}})};function g(){axios.post("/exportReceipt",o,{responseType:"blob"}).then(i=>{var c=document.createElement("a");document.body.appendChild(c),c.style="display: none";var x=new Blob([i.data],{type:i.headers["content-type"]});const d=document.createElement("a");d.href=window.URL.createObjectURL(x),d.download=`receipts${new Date().getTime()}.xlsx`,d.click()})}return{doSearch:f,resetForm:l,exportExcel:g,form:o,formatter:t}}},P={class:"py-2"},E={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},O={class:"bg-white shadow sm:rounded-t-lg flex justify-between space-x-2 items-end py-2 px-2 md:px-2"},T={class:"grid grid-cols-1 md:grid-cols-6 gap-2 w-full"},U={class:"col-span-1 sm:col-span-1"},L={class:"py-2"},F={class:"mt-1 flex rounded-md shadow-sm"},Y={class:"col-span-1 sm:col-span-1"},A={class:"py-2"},I={class:"mt-1 flex rounded-md shadow-sm"},z={class:"col-span-1 sm:col-span-1"},G={class:"py-2"},q={key:0,class:"mt-1 flex rounded-md shadow-sm"},H={class:"col-span-1 sm:col-span-1"},J={class:"py-2"},K={class:"mt-4 flex"},Q={class:"col-span-1 sm:col-span-1"},W={class:"py-2"},X={class:"mt-4 flex"},Z={class:"col-span-1 sm:col-span-1"},$={class:"py-2"},ee={class:"mt-4 flex"},te={class:"mb-2 py-2 px-2 md:px-2 bg-white shadow rounded-b-lg flex justify-between"},se={class:"flex"},oe={class:"flex"},le={key:0,class:"bg-white overflow-auto shadow-xl sm:rounded-lg"},ae={class:"min-w-full divide-y divide-gray-200"},ie={class:"bg-white divide-y divide-gray-200 text-sm"},ne={class:"px-6 py-3 text-xs font-medium whitespace-nowrap"},re={class:"px-6 py-3 text-xs font-medium whitespace-nowrap"},de={class:"px-6 py-3 text-xs font-medium whitespace-nowrap"},ce={class:"px-6 py-3 text-xs font-medium whitespace-nowrap"},pe={class:"px-6 py-3 text-xs font-medium whitespace-nowrap"},me={class:"px-6 py-3 text-xs font-medium whitespace-nowrap"},xe={class:"px-6 py-3 text-xs font-medium whitespace-nowrap"},ue={class:"px-6 py-3 text-xs font-medium whitespace-nowrap"},fe={key:1,class:"w-full block mt-4"},ge={class:"text-xs text-gray-600"},ye={key:2};function be(_,t,o,l,f,g){const i=p("VueDatePicker"),c=p("multiselect"),x=p("pagination"),d=p("app-layout");return n(),R(d,null,{header:y(()=>t[6]||(t[6]=[e("h2",{class:"font-semibold text-xl text-white leading-tight"},"Bill Receipt Report",-1)])),default:y(()=>[e("div",P,[e("div",E,[t[17]||(t[17]=e("label",{for:"name",class:"block text-sm font-bold text-indigo-700 mt-4"},"Report Preference",-1)),e("div",O,[e("div",T,[e("div",U,[e("div",L,[t[8]||(t[8]=e("label",{for:"general",class:"block text-sm font-medium text-gray-700"},"General ",-1)),e("div",F,[t[7]||(t[7]=e("span",{class:"z-10 leading-snug font-normal text-center text-gray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-2"},[e("i",{class:"fas fa-user"})],-1)),V(e("input",{type:"text","onUpdate:modelValue":t[0]||(t[0]=s=>l.form.general=s),name:"general",id:"general",class:"pl-10 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded sm:text-sm border-gray-200",placeholder:"Customer/Company Name etc.",tabindex:"1"},null,512),[[D,l.form.general]])])])]),e("div",Y,[e("div",A,[t[9]||(t[9]=e("label",{for:"date",class:"block text-sm font-medium text-gray-700"},"Receipt Date ",-1)),e("div",I,[u(i,{modelValue:l.form.date,"onUpdate:modelValue":t[1]||(t[1]=s=>l.form.date=s),range:{partialRange:!0},placeholder:"Please choose Order Date","enable-time-picker":!1,"model-type":"yyyy-MM-dd",id:"order_date",class:"text-gray-400 text-sm focus:ring focus:ring-indigo-500 focus:border-indigo-500 placeholder:text-xs focus:ring-opacity-10 focus:outline-none"},null,8,["modelValue"])])])]),e("div",z,[e("div",G,[t[10]||(t[10]=e("label",{for:"bill_id",class:"block text-sm font-medium text-gray-700"},"Bill Name ",-1)),o.bill_list.length!==0?(n(),r("div",q,[u(c,{"deselect-label":"Selected already",options:o.bill_list,"track-by":"id",label:"name",modelValue:l.form.bill_id,"onUpdate:modelValue":t[2]||(t[2]=s=>l.form.bill_id=s),"allow-empty":!0,multiple:!0},null,8,["options","modelValue"])])):m("",!0)])]),e("div",H,[e("div",J,[t[11]||(t[11]=e("label",{class:"block text-sm font-medium text-gray-700"},"Yesterday Collection ",-1)),e("div",K,a(o.yesterday_collection.toLocaleString("en-US")),1)])]),e("div",Q,[e("div",W,[t[12]||(t[12]=e("label",{class:"block text-sm font-medium text-gray-700"},"Today Collection ",-1)),e("div",X,a(o.today_collection.toLocaleString("en-US")),1)])]),e("div",Z,[e("div",$,[t[13]||(t[13]=e("label",{class:"block text-sm font-medium text-gray-700"},"Total Search Result ",-1)),e("div",ee,a(o.select_total.toLocaleString("en-US")),1)])])])]),e("div",te,[e("div",se,[e("a",{onClick:t[3]||(t[3]=(...s)=>l.doSearch&&l.doSearch(...s)),class:"cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},t[14]||(t[14]=[b("Search "),e("i",{class:"ml-1 fa fa-search text-white"},null,-1)])),e("a",{onClick:t[4]||(t[4]=(...s)=>l.resetForm&&l.resetForm(...s)),class:"ml-2 cursor-pointer inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition"},t[15]||(t[15]=[b("Reset "),e("i",{class:"ml-1 fa fa-undo-alt text-white"},null,-1)]))]),e("div",oe,[e("button",{onClick:t[5]||(t[5]=(...s)=>l.exportExcel&&l.exportExcel(...s)),type:"button",class:"inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition"},"Export")])]),o.receipt_records.data?(n(),r("div",le,[e("table",ae,[t[16]||(t[16]=e("thead",{class:"bg-gray-50"},[e("tr",null,[e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Bill Name"),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Bill For"),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Invoice Number"),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Customer ID"),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Issue Amount"),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Receipt Amount"),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Receipt Date"),e("th",{scope:"col",class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"}," Customer Paid Date")])],-1)),e("tbody",ie,[(n(!0),r(C,null,S(o.receipt_records.data,s=>(n(),r("tr",{key:s.id},[e("td",ne,a(s.bill_name),1),e("td",re,a(s.year)+"-"+a(s.month),1),e("td",de,a(s.bill_number),1),e("td",ce,a(s.ftth_id),1),e("td",pe,a(s.issue_amount),1),e("td",me,a(s.collected_amount),1),e("td",xe,a(s.created_at),1),e("td",ue,a(s.receipt_date),1)]))),128))])])])):m("",!0),o.receipt_records.total?(n(),r("span",fe,[e("label",ge,a(o.receipt_records.data.length)+" Receipt_records in Current Page. Total Number of Receipt_records : "+a(o.receipt_records.total),1)])):m("",!0),o.receipt_records.links?(n(),r("span",ye,[u(x,{class:"mt-4",links:o.receipt_records.links},null,8,["links"])])):m("",!0)])])]),_:1})}const De=B(N,[["render",be]]);export{De as default};
