import{A as w}from"./AppLayout-06ovi2Jk.js";import{P as y}from"./Pagination-LBr2dpyI.js";import{J as v,r as _,q as h,B as k,s as S,c as U,w as c,N as b,o as i,a as s,h as C,k as n,v as E,x as a,d as m,t as u,e as p,z as g}from"./app-uevufprU.js";import{_ as M}from"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DropdownLink-C_GraEJX.js";const P={name:"RadiusConfig",components:{AppLayout:w,Pagination:y},props:{config:Object,errors:Object},setup(r){const e=v({id:null,server_url:null,port:null,enabled:null,admin_user:null,admin_password:null});let l=_(!1);function o(){l.value?(e._method="PUT",b.post("/radiusconfig/"+e.id,e,{onSuccess:t=>{Toast.fire({icon:"success",title:t.props.flash.message})},onError:t=>{console.log("error ..".errors)}})):(e._method="POST",b.post("/radiusconfig",e,{preserveState:!0,onSuccess:t=>{Toast.fire({icon:"success",title:t.props.flash.message})},onError:t=>{console.log("error ..".errors)}}))}function f(t){l.value=!0,e.id=t.id,e.enabled=t.enabled==1,e.server_url=t.server,e.admin_user=t.admin_user,e.admin_password=t.admin_password,e.port=t.port}return h(()=>{r.config.length>0&&f(r.config[0])}),k(()=>{}),{form:e,submit:o,editMode:l}}},A={class:"py-2"},V={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},B={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg"},N={class:"bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"},R={class:""},T={class:"mb-4"},j={class:"inline-flex"},O={class:"mb-4"},D={key:0,class:"text-red-500"},L={class:"mb-4"},q={key:0,class:"text-red-500"},z={class:"mb-4"},J={key:0,class:"text-red-500"},F={class:"mb-4"},G={key:0,class:"text-red-500"},H={class:"bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"},I={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},K={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},Q={class:"flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto"},W={"wire:click.prevent":"submit",type:"submit",class:"inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"},X={class:"mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto"};function Y(r,e,l,o,f,t){const x=S("app-layout");return i(),U(x,null,{header:c(()=>e[7]||(e[7]=[s("h2",{class:"font-semibold text-xl text-white leading-tight"},"Radius Configuration",-1)])),default:c(()=>[s("div",A,[s("div",V,[s("div",B,[s("form",{onSubmit:e[6]||(e[6]=C((...d)=>o.submit&&o.submit(...d),["prevent"]))},[s("div",N,[s("div",R,[s("div",T,[s("label",j,[e[8]||(e[8]=s("label",{for:"enabled",class:"text-gray-700 text-sm font-bold mt-1"},"ENABLE:",-1)),n(s("input",{id:"enabled",class:"text-green-500 w-6 h-6 ml-2 focus:ring-green-400 focus:ring-opacity-25 border border-gray-300 rounded",type:"checkbox","onUpdate:modelValue":e[0]||(e[0]=d=>o.form.enabled=d)},null,512),[[E,o.form.enabled]])])]),s("div",O,[e[9]||(e[9]=s("label",{for:"server_url",class:"block text-gray-700 text-sm font-bold mb-2"},"Server:",-1)),n(s("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"server_url",placeholder:"Enter Radius Server","onUpdate:modelValue":e[1]||(e[1]=d=>o.form.server_url=d)},null,512),[[a,o.form.server_url]]),r.$page.props.errors.server_url?(i(),m("div",D,u(r.$page.props.errors.server_url),1)):p("",!0)]),s("div",L,[e[10]||(e[10]=s("label",{for:"port",class:"block text-gray-700 text-sm font-bold mb-2"},"Port:",-1)),n(s("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"port",placeholder:"Enter Radius Port","onUpdate:modelValue":e[2]||(e[2]=d=>o.form.port=d)},null,512),[[a,o.form.port]]),r.$page.props.errors.port?(i(),m("div",q,u(r.$page.props.errors.port),1)):p("",!0)]),s("div",z,[e[11]||(e[11]=s("label",{for:"admin_user",class:"block text-gray-700 text-sm font-bold mb-2"},"Admin User:",-1)),n(s("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"admin_user",placeholder:"Enter Admin User","onUpdate:modelValue":e[3]||(e[3]=d=>o.form.admin_user=d)},null,512),[[a,o.form.admin_user]]),r.$page.props.errors.admin_user?(i(),m("div",J,u(r.$page.props.errors.admin_user),1)):p("",!0)]),s("div",F,[e[12]||(e[12]=s("label",{for:"admin_password",class:"block text-gray-700 text-sm font-bold mb-2"},"Admin Password:",-1)),n(s("input",{type:"text",class:"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md",id:"admin_password",placeholder:"Enter Admin Password","onUpdate:modelValue":e[4]||(e[4]=d=>o.form.admin_password=d)},null,512),[[a,o.form.admin_password]]),r.$page.props.errors.admin_password?(i(),m("div",G,u(r.$page.props.errors.admin_password),1)):p("",!0)])])]),s("div",H,[s("span",I,[n(s("button",K,"Save",512),[[g,!o.editMode]])]),s("span",Q,[n(s("button",W,"Update",512),[[g,o.editMode]])]),s("span",X,[s("button",{onClick:e[5]||(e[5]=(...d)=>r.closeModal&&r.closeModal(...d)),type:"button",class:"inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5"},"Cancel")])])],32)])])])]),_:1})}const re=M(P,[["render",Y]]);export{re as default};
