package com.example.sdk_test_demo.wxapi;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.widget.Toast;

import com.example.sdk_test_demo.R;
import com.mchsdk.paysdk.MCApiFactory;
import com.mchsdk.paysdk.config.MCHConstant;
import com.tencent.mm.sdk.modelbase.BaseReq;
import com.tencent.mm.sdk.modelbase.BaseResp;
import com.tencent.mm.sdk.openapi.IWXAPI;
import com.tencent.mm.sdk.openapi.IWXAPIEventHandler;
import com.tencent.mm.sdk.openapi.WXAPIFactory;
public class WXPayEntryActivity extends Activity implements IWXAPIEventHandler {

	private static final String TAG = "WXPayEntryActivity";

	private IWXAPI api;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		
		setContentView(R.layout.mch_pay_result);
		api = WXAPIFactory.createWXAPI(this, MCHConstant.getInstance().getWxAppId());
		api.handleIntent(getIntent(), this);
		api.registerApp(MCHConstant.getInstance().getWxAppId());
	}
	@Override
	protected void onNewIntent(Intent intent) {
		super.onNewIntent(intent);
		setIntent(intent);
		api.handleIntent(intent, this);
	}
	@Override
	public void onReq(BaseReq req) {
	}
	@Override
	public void onResp(BaseResp baseResp) {
//		int i = BaseResp.ErrCode.ERR_AUTH_DENIED;
//		i = BaseResp.ErrCode.ERR_COMM;
		Log.e(TAG, "fun#onResp errCode = " + baseResp.errCode + " errStr = " + baseResp.errStr + " openId = "+ baseResp.openId);
		 if(5 == baseResp.getType() && 0 == baseResp.errCode){
			 Log.e(TAG, "fun#onResp errCode = " + baseResp.errCode);
			 MCApiFactory.getMCApi().wxResult(baseResp);
		 }else{
			 Toast.makeText(getApplicationContext(), "微信支付失败，请重试！", Toast.LENGTH_SHORT).show();
		 }
		WXPayEntryActivity.this.finish();
	}
}