// JScript source code
	  //var iYearStart,iMonthStart,iDayStart,iHourStart,iMinuteStart;
	  function validateControlContent(flag)
//			  bb=document.getElementsByName("InputTimeStart")[0].value;
//			  bb=document.getElementsByName("InputTimeEnd")[0].value;
		  }
			  }
		  }
		  }
	  }
	  function validateInputDate(date_in,time_in)
		  var aas=date_in.split("-");
//		  var bbs=time_in.split(":");
		  var re1=/^\d{4}-\d{2}-\d{2}$/;
		  var re2 = /^\d{2}:\d{2}$/;

		  if (!re1.test(date_in)) {
		      alert("��������ȷ�ĸ�ʽ");
		      return 1;
		  }
		  //		  if(!re1.test(date_in)||!re2.test(time_in))
		  //		  {
		  //			  alert("��������ȷ�ĸ�ʽ");
		  //			  return 1;
		  //		  }
		  if(parseInt(aas[1],10)>12||parseInt(aas[1],10)<1)
			  alert("�·ݴ���");
			  return 1;
		  }
		  if(parseInt(aas[1],10)==2)
				  return 1;
			  }
			  }else{
				  alert("���ڴ���");
				  return 1;
			  }
		  }
		  if(parseInt(aas[1],10)==1 ||parseInt(aas[1],10)==3 ||parseInt(aas[1],10)==5 ||parseInt(aas[1],10)==7 ||parseInt(aas[1],10)==8 ||parseInt(aas[1],10)==10		||parseInt(aas[1],10)==12)
			  alert("���ڴ���");
			  return 1;
		  }
		  if(parseInt(aas[1],10)==4 ||parseInt(aas[1],10)==6 ||parseInt(aas[1],10)==9 ||parseInt(aas[1],10)==11)
		  if(parseInt(aas[2],10)>31)
			  alert("���ڴ���");
			  return 1;
		  }

//		  if(parseInt(bbs[0],10)>59||parseInt(bbs[1],10)>59)
//		  {
//			  alert("ʱ�����");
//			  return 1;
//		  }
	  function GetFormatInputDate()
	  function GetFormatTime()
	  function CheckNumber(int_in)
	  function initializeTimeEnd()