package com.example.heart_dog;

import androidx.appcompat.app.AppCompatActivity;
import androidx.constraintlayout.widget.Group;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.concurrent.ExecutionException;

public class ForgottenPassword extends AppCompatActivity {

    Button send;
    EditText email;
    String Email;
    String result = "";
    String result_code;
    Intent main;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_forgotten_password);

        email = findViewById(R.id.et_email);

        send = findViewById(R.id.btn_send);
        send.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Email = email.getText().toString().trim();

                JSONObject json = new JSONObject();
                try {
                    json.put("Email", Email);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                try {
                    result = new PostJSON().execute("http://teame-iot.calit2.net/heartdog/app/forget_pw", json.toString()).get();
                } catch (ExecutionException e) {
                    e.printStackTrace();
                } catch (InterruptedException e) {
                    e.printStackTrace();
                }
                JSONObject json_data = null;
                try {
                    json_data = new JSONObject(result);
                    Log.d("asdf5", "receive json: " + json_data.toString());
                    result_code = (json_data.optString("result_code"));
                    Log.d("asdf6", "result_code: " + result_code);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                if (result_code.equals("0")) { // 이메일 링크가 발송된 경우
                    Toast.makeText(ForgottenPassword.this, "Password change link is sent", Toast.LENGTH_SHORT).show();
                    main = new Intent(getApplicationContext(), MainActivity.class);
                    startActivity(main);
                }
                else if (result_code.equals("1")) { // 잘못된 이메일인 경우
                    Toast.makeText(ForgottenPassword.this, "Wrong Email address", Toast.LENGTH_SHORT).show();
                    main = new Intent(getApplicationContext(), MainActivity.class);
                    startActivity(main);
                }
                else { // 커뮤니케이션 오류
                    Toast.makeText(ForgottenPassword.this, "Communication Error", Toast.LENGTH_LONG).show();
                    main = new Intent(getApplicationContext(), MainActivity.class);
                    startActivity(main);
                }

            }
        });
    }
}
