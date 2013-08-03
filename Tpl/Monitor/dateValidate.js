// JScript source code
	  //var iYearStart,iMonthStart,iDayStart,iHourStart,iMinuteStart;	  var iYear,iMonth,iDay,iHour,iMinute;	  //var iYearEnd,iMonthEnd,iDayEnd,iHourEnd,iMinuteEnd;	  var strStartTime,strEndTime;
	  function validateControlContent(flag)	  {		  var aa,bb;		  if(flag == 1)// Start 		  {			  aa=document.getElementsByName("InputeDateStart")[0].value;
//			  bb=document.getElementsByName("InputTimeStart")[0].value;		  }		  else		  {			  aa=document.getElementsByName("InputeDateEnd")[0].value;
//			  bb=document.getElementsByName("InputTimeEnd")[0].value;
		  }		  if(0 == validateInputDate(aa,bb))		  {			  if(flag == 1)			  {				  strStartTime = GetFormatTime();			  }			  else			  {				  strEndTime = GetFormatTime();
			  }			  return 0;
		  }		  else		  {			  return 1;
		  }
	  }
	  function validateInputDate(date_in,time_in)	  {
		  var aas=date_in.split("-");
//		  var bbs=time_in.split(":");
		  var re1=/^\d{4}-\d{2}-\d{2}$/;
		  var re2 = /^\d{2}:\d{2}$/;

		  if (!re1.test(date_in)) {
		      alert("请输入正确的格式");
		      return 1;
		  }
		  //		  if(!re1.test(date_in)||!re2.test(time_in))
		  //		  {
		  //			  alert("请输入正确的格式");
		  //			  return 1;
		  //		  }
		  if(parseInt(aas[1],10)>12||parseInt(aas[1],10)<1)		  {
			  alert("月份错误");
			  return 1;
		  }
		  if(parseInt(aas[1],10)==2)		  if((parseInt(aas[0])%4==0 && parseInt(aas[0])%100!=0) || (parseInt(aas[0])%4==0 && parseInt(aas[0])%100==0 && parseInt(aas[0])%400==0)){			  if((parseInt(aas[2],10)>29))			  {				  alert("日期错误");
				  return 1;
			  }
			  }else{			  if((parseInt(aas[2],10)>28))			  {
				  alert("日期错误");
				  return 1;
			  }
		  }
		  if(parseInt(aas[1],10)==1 ||parseInt(aas[1],10)==3 ||parseInt(aas[1],10)==5 ||parseInt(aas[1],10)==7 ||parseInt(aas[1],10)==8 ||parseInt(aas[1],10)==10		||parseInt(aas[1],10)==12)		  if(parseInt(aas[2],10)>31)		  {
			  alert("日期错误");
			  return 1;
		  }
		  if(parseInt(aas[1],10)==4 ||parseInt(aas[1],10)==6 ||parseInt(aas[1],10)==9 ||parseInt(aas[1],10)==11)
		  if(parseInt(aas[2],10)>31)		  {
			  alert("日期错误");
			  return 1;
		  }

//		  if(parseInt(bbs[0],10)>59||parseInt(bbs[1],10)>59)
//		  {
//			  alert("时间错误");
//			  return 1;
//		  }		  iYear = aas[0];		  iMonth = aas[1];		  iDay = aas[2];//		  iHour = bbs[0];//		  iMinute = bbs[1];		  return 0;	  }
	  function GetFormatInputDate()	  {		  if(0 == validateControlContent(1) && 0 == validateControlContent(2))		  {			  //var fd = GetFormatTime();			  //return fd;			  if(strStartTime < strEndTime)			  {				  alert(strStartTime + " -> " + strEndTime);				  return 0;			  }			  else			  {				  alert(strEndTime + " -> " + strStartTime);				  return 1;			  }		  }	  }
	  function GetFormatTime()	  {		  var year = CheckNumber(iYear);		  var month = CheckNumber(iMonth);		  var day = CheckNumber(iDay);		  //var h=CheckNumber(iHour);		  //var m=CheckNumber(iMinute);		  //var s=CheckNumber(myDate.getSeconds());		  var strDatetime = year +'-'+ month +'-'+ day ;//		  var strDatetime = year +'-'+ month +'-'+ day +' '+h+":"+m;		  return strDatetime;	  }
	  function CheckNumber(int_in)	  {		  int_in = parseInt(int_in);		  if(int_in <= 9)		  {			  return '0'+ int_in.toString();		  }		  else		  {			  return int_in;		  }	  }
	  function initializeTimeEnd()	  {	  var myDate=new Date();	  var month = CheckNumber(myDate.getMonth()+1);	  var day = CheckNumber(myDate.getDate());	  var h=CheckNumber(myDate.getHours());	  var m=CheckNumber(myDate.getMinutes());	  var strDate = myDate.getFullYear() +'-'+ month +'-'+ day;	  var strtime = h+":"+m;	  document.getElementsByName("InputeDateEnd")[0].value = strDate ;	  //document.getElementsByName("InputTimeEnd")[0].value = strtime ;	  }
