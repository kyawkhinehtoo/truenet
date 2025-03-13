import{T as b,d,b as a,w as n,u as t,F as k,o as m,a as o,t as p,Z as x,e as u,c as _,f,i as S,n as v,h as V}from"./app-uevufprU.js";import{A as $}from"./AuthenticationCard-C9ZAv8td.js";import{_ as B}from"./Checkbox-I84_FBdl.js";import{_ as g,a as c}from"./TextInput-kGR725jG.js";import{_ as w}from"./InputLabel-Couc59Gx.js";import{_ as h}from"./PrimaryButton-B6k9Jhee.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";const C={class:"flex flex-col items-center align-middle uppercase text-gray-400"},F=["src"],L={key:0,class:"mb-4 font-medium text-sm text-green-600 dark:text-green-400"},N={class:"mt-4"},P={class:"block mt-4"},q={class:"flex items-center"},E={class:"flex items-center justify-end mt-4"},z={__name:"Login",props:{canResetPassword:Boolean,status:String},setup(i){const s=b({email:"",password:"",remember:!1}),y=()=>{s.transform(r=>({...r,remember:s.remember?"on":""})).post(route("login"),{onFinish:()=>s.reset("password")})};return(r,e)=>(m(),d(k,null,[a(t(x),null,{default:n(()=>[o("title",null,"Login - "+p(r.$page.props.application_name),1),e[3]||(e[3]=o("meta",{name:"description",content:"ISP Manager OSS BSS SYSTEM"},null,-1)),e[4]||(e[4]=o("link",{rel:"icon",type:"image/png",href:"/storage/logos/favicon.png"},null,-1))]),_:1}),a($,null,{logo:n(()=>[o("span",C,[r.$page.props.logo_small?(m(),d("img",{key:0,src:`/storage/${r.$page.props.logo_small}`,alt:"Logo",class:"w-32 opacity-90 select-none"},null,8,F)):u("",!0),e[5]||(e[5]=o("h2",{class:"mt-2 font-bold antialiased flex"},"Operation & Billing Support System",-1))])]),default:n(()=>[i.status?(m(),d("div",L,p(i.status),1)):u("",!0),o("form",{onSubmit:V(y,["prevent"])},[o("div",null,[a(w,{for:"email",value:"Email"}),a(g,{id:"email",modelValue:t(s).email,"onUpdate:modelValue":e[0]||(e[0]=l=>t(s).email=l),type:"email",class:"mt-1 block w-full",required:"",autofocus:"",autocomplete:"username"},null,8,["modelValue"]),a(c,{class:"mt-2",message:t(s).errors.email},null,8,["message"])]),o("div",N,[a(w,{for:"password",value:"Password"}),a(g,{id:"password",modelValue:t(s).password,"onUpdate:modelValue":e[1]||(e[1]=l=>t(s).password=l),type:"password",class:"mt-1 block w-full",required:"",autocomplete:"current-password"},null,8,["modelValue"]),a(c,{class:"mt-2",message:t(s).errors.password},null,8,["message"])]),o("div",P,[o("label",q,[a(B,{checked:t(s).remember,"onUpdate:checked":e[2]||(e[2]=l=>t(s).remember=l),name:"remember"},null,8,["checked"]),e[6]||(e[6]=o("span",{class:"ms-2 text-sm text-gray-600 dark:text-gray-400"},"Remember me",-1))])]),o("div",E,[i.canResetPassword?(m(),_(t(S),{key:0,href:r.route("password.request"),class:"underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"},{default:n(()=>e[7]||(e[7]=[f(" Forgot your password? ")])),_:1},8,["href"])):u("",!0),a(h,{class:v(["ms-4",{"opacity-25":t(s).processing}]),disabled:t(s).processing},{default:n(()=>e[8]||(e[8]=[f(" Log in ")])),_:1},8,["class","disabled"])])],32)]),_:1})],64))}};export{z as default};
