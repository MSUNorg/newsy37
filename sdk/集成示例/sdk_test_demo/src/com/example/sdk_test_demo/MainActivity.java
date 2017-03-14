package com.example.sdk_test_demo;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Toast;

import com.fanwei.jubaosdk.common.util.SdkConst;
import com.fanwei.jubaosdk.shell.FWPay;
import com.mchsdk.paysdk.GPExitResult;
import com.mchsdk.paysdk.GPUserResult;
import com.mchsdk.paysdk.IGPExitObsv;
import com.mchsdk.paysdk.IGPUserObsv;
import com.mchsdk.paysdk.MCApiFactory;
import com.mchsdk.paysdk.TestActivity;
import com.mchsdk.paysdk.payCallback;
import com.mchsdk.paysdk.entity.OrderInfo;
import com.mchsdk.paysdk.utils.MCLog;

public class MainActivity extends Activity {
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		findViewById(R.id.login);
		findViewById(R.id.btnPay).setOnClickListener(myOnlickListener);
		findViewById(R.id.btnExit).setOnClickListener(myOnlickListener);
		findViewById(R.id.login).setOnClickListener(myOnlickListener);
		findViewById(R.id.btn_setTestParams).setOnClickListener(
				myOnlickListener);
		findViewById(R.id.btn_setTestParams).setVisibility(View.VISIBLE);
		init();
	}

	private void init() {
		MCLog.isDebug = true;
		FWPay.initialize(this, true);
		/** 个人中心里面点击退出登录执行的退出回调接口,根据实际需要选择 **/
		MCApiFactory.getMCApi().initExitFromPersonInfoParams(mExitObsv);
		/** 参数为点击个人中心退出个人中心界面之后跳转的activity,根据需要选择 */
		MCApiFactory.getMCApi()
				.initExitFromPersonInfoParams(MainActivity.class);
		/** 设置测试 打包参数 ，设置以后，如果apk没有在平台打包，sdk会调用这里面设置的参数 */
		MCApiFactory.getMCApi().initTestParams("0"// 渠道id
				, "自然注册"// 渠道名称
				, "12"// 游戏id
				, "决战中洲"// 游戏名称
				, "520D9380CF1B71E89");// 游戏appid
		/** 渠道信息优先级，真实打包 > 这里填写的测试信息 > sdk里面的测试参数 */
	}

	/** * 退出界面回调接口 */
	private IGPExitObsv mExitObsv = new IGPExitObsv() {
		@Override
		public void onExitFinish(GPExitResult exitResult) {
			switch (exitResult.mResultCode) {
			case GPExitResult.GPSDKExitResultCodeError:
				writeLog("退出回调:调用退出弹框失败");
				break;
			case GPExitResult.GPSDKExitResultCodeExitGame:
				// 注销
				MCApiFactory.getMCApi().sdkLogOff(mExitObsv);
				// 关闭悬浮窗
				MCApiFactory.getMCApi().stopFloating(MainActivity.this);
				// 你自己的退出逻辑，退出程序
				System.exit(0);
				finish();
				break;
			case GPExitResult.GPSDKExitResultCodeCloseWindow:
				writeLog("退出回调:调用关闭退出弹框");
				break;
			case GPExitResult.GPSDKResultCodeOfPersonInfo:
				writeLog("退出回调:执行SDK个人中心退出方法");
				// 关闭悬浮窗
				MCApiFactory.getMCApi().stopFloating(MainActivity.this);
				// 下面是退出逻辑
				Intent MyIntent = new Intent(Intent.ACTION_MAIN);
				MyIntent.addCategory(Intent.CATEGORY_HOME);
				startActivity(MyIntent);
				break;
			case GPExitResult.GPSDKResultCodeOfLogOffSucc:
				writeLog("退出回调:注销成功方法回调");
				break;
			case GPExitResult.GPSDKResultCodeOfLogOffFail:
				writeLog("退出回调:注销失败方法回调");
				break;
			}
		}
	};
	OnClickListener myOnlickListener = new OnClickListener() {

		@Override
		public void onClick(View v) {
			switch (v.getId()) {
			case R.id.btnPay:
				OrderInfo o = new OrderInfo();
				o.setAmount(200);// 物品价格,单位分
				o.setExtendInfo("这里面填写透传的信息"); // 用于确认交易给玩家发送商品
				o.setProductDesc("10钻石"); // 物品描述
				o.setProductName("钻石");// 物品名称
				MCApiFactory.getMCApi().pay(MainActivity.this, null, o,
						payCallback);
				break;
			case R.id.btnExit:
				MCApiFactory.getMCApi().exit(MainActivity.this, mExitObsv);
				break;
			case R.id.login:
				MCApiFactory.getMCApi()
						.startlogin(MainActivity.this, mUserObsv);
				break;
			case R.id.btn_setTestParams:
				startActivity(new Intent(getApplicationContext(),
						TestActivity.class));
				break;
			}
		}
	};
	/** 支付结果回调 */
	public static payCallback payCallback = new payCallback() {
		public void callback(int result) {
			if (result == 1) {
				// 支付成功
				System.out.println("客户端支付结果：" + result);
				return;
			}
		}
	};

	/** * 登录回调接口 */
	private IGPUserObsv mUserObsv = new IGPUserObsv() {
		@Override
		public void onFinish(GPUserResult result) {
			switch (result.getmErrCode()) {
			case GPUserResult.USER_RESULT_LOGIN_FAIL:
				writeLog("登录回调:登录失败");
				break;
			case GPUserResult.USER_RESULT_LOGIN_SUCC:
				MCApiFactory.getMCApi().startFloating(MainActivity.this);
				String key = "mengchuang";
				String accountNo = result.getAccountNo();// 登录用户id
				String sign = result.getSign();// 登录签名
				String timeStamp = result.getTimeStamp();// 登录时间戳
				String account = result.getAccount();// 登录用户账号
				String tempString = "" + accountNo + account + key + timeStamp;
				String temp2 = PaykeyUtil.stringToMD5(tempString);
				writeLog("accountNo" + accountNo);
				writeLog("sign" + sign);
				writeLog("timeStamp" + timeStamp);
				writeLog("account" + account);
				writeLog("登录回调:登录成功sign = " + sign);
				writeLog("登录回调:登录成功temp = " + temp2);
				break;
			}
		}
	};

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent msg) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			MCApiFactory.getMCApi().exit(MainActivity.this, mExitObsv);
		}
		if (keyCode == KeyEvent.KEYCODE_HOME) {
			System.out.println("11111111111111111111111");
		}
		return true;
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode,
			Intent intent) {
		if (requestCode == SdkConst.REQUESTCODE
				&& resultCode == SdkConst.RESULTCODE) {
			String code = intent.getStringExtra("code");
			String message = intent.getStringExtra("message");
			MCApiFactory.getMCApi().jbyResult(code, message);
			if (code.equals("0")) {// success

			} else if (code.equals("1")) {// fail

			} else if (code.equals("2")) {// cancel

			}
		}
		super.onActivityResult(requestCode, resultCode, intent);
	}

	public static void writeLog(String msg) {
		Log.e("pay----", msg);
	}
}
