package com.example.heart_dog;

import androidx.appcompat.app.AppCompatActivity;

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

public class MainActivity extends AppCompatActivity {

    Button make, forget, signIn;
    EditText email, password;
    String email_str, password_str;
    String result = "";
    String result_code;
    Intent home, create, forgot;
    public static String USN = "";

    public String getEmail() {
        return email_str;
    }
    public void setEmail(String email_str) {
        this.email_str = email_str;
    }
    public String getPassword() {
        return password_str;
    }
    public void setPassword(String password_str) {
        this.password_str = password_str;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        make = findViewById(R.id.btn_make);
        forget = findViewById(R.id.btn_forget);
        signIn = findViewById(R.id.btn_signIn);
        email = findViewById(R.id.et_email);
        password = findViewById(R.id.et_pw);

        signIn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) { // 로그인 버튼을 누른 경우
                setEmail(email.getText().toString().trim());
                setPassword(password.getText().toString().trim());

                if (getEmail().isEmpty() || getPassword().isEmpty()) { // 이메일이나 비밀번호를 입력하지 않은 경우
                    if (getEmail().isEmpty()) {
                        Toast.makeText(MainActivity.this, "Input your E-mail Address.", Toast.LENGTH_SHORT).show();
                    } else if (getPassword().isEmpty()) {
                        Toast.makeText(MainActivity.this, "Input your Password.", Toast.LENGTH_SHORT).show();
                    }
                } else { // 이메일이나 비밀번호를 입력한 경우
                    JSONObject json = new JSONObject();
                    try {
                        json.put("Email", getEmail());
                        json.put("Password", getPassword());
                        Log.d("asdf", json.toString());
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                    if (email.length() > 0) {
                        try {
                            result = new PostJSON().execute("http://teame-iot.calit2.net/heartdog/app/signin", json.toString()).get();
                        } catch (ExecutionException e) {
                            e.printStackTrace();
                        } catch (InterruptedException e) {
                            e.printStackTrace();
                        } catch (Exception e) {
                            e.printStackTrace();
                        }

                    }
                    try {
                        JSONObject json_data = new JSONObject(result);
                        USN = (json_data.optString("USN"));
                        result_code = (json_data.optString("result_code"));
                    } catch (Exception e) {
                        Log.e("Fail 3", e.toString());
                    }
                    if (result_code.equals("0")) { // 로그인에 성공하는 경우
                        Toast.makeText(MainActivity.this, "Welcome!", Toast.LENGTH_SHORT).show();
                        home = new Intent(getApplicationContext(), Home.class);
                        startActivity(home);
                        overridePendingTransition(R.anim.fade, R.anim.hold);
                    } else if (result_code.equals("1")) { // 이메일이 존재하지 않는 경우
                        Toast.makeText(MainActivity.this, "Not exist Email!", Toast.LENGTH_SHORT).show();
                    } else if (result_code.equals("2")) { // 비밀번호가 틀린 경우
                        Toast.makeText(MainActivity.this, "Check your account !", Toast.LENGTH_SHORT).show();
                    } else { // 통신 오류
                        Toast.makeText(MainActivity.this, "Communicate Error", Toast.LENGTH_SHORT).show();
                    }
                }
            }
        });

        make.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View w) { // 회원가입 버튼을 누른 경우
                create = new Intent(getApplicationContext(), SignUp.class);
                startActivity(create);

                overridePendingTransition(R.anim.fade, R.anim.hold);
            }
        });
        forget.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View w) { // 비밀번호 찾기 버튼을 누른 경우
                forgot = new Intent(getApplicationContext(), ForgottenPassword.class);
                startActivity(forgot);

                overridePendingTransition(R.anim.fade, R.anim.hold);
            }
        });
    }
}
