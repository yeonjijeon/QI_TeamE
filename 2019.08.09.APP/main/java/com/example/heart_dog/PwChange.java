package com.example.heart_dog;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import android.widget.Toolbar;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.concurrent.ExecutionException;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class PwChange extends AppCompatActivity {
    EditText password, newPw, conNewPw;
    String Password, NewPw, ConNewPw;
    Button change;
    String result = "";
    String result_code;
    Intent home;
    String usn = MainActivity.USN;
    boolean b1;

    public static final Pattern VALID_PASSWOLD_REGEX_ALPHA_NUM = Pattern.compile("^(?=.*[A-Za-z])(?=.*\\d)(?=.*[$@$!%*#?&])[A-Za-z\\d$@$!%*#?&]{8,16}$"); // 8자리 ~ 16자리까지 가능


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pw_change);

        password = findViewById(R.id.et_pw);
        newPw = findViewById(R.id.et_new_pw);
        conNewPw = findViewById(R.id.et_con_new_pw);
        change = findViewById(R.id.btn_change);

        newPw.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                NewPw = newPw.getText().toString();
                b1 = validatePassword(NewPw);

                if(b1 == true) {
                    newPw.setText("* Available Password");
                }
                else {
                    newPw.setText("* Must contain 1 letters, 1 number, 1 special character, between 8-16 long");
                }
            }

            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                NewPw = newPw.getText().toString();
                b1 = validatePassword(NewPw);

                if(b1 == true) {
                    newPw.setText("* Available Password");
                }
                else {
                    newPw.setText("* Must contain 1 letters, 1 number, 1 special character, between 8-16 long");
                }
            }

            @Override
            public void afterTextChanged(Editable editable) {

            }
        });

        change.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                Password = password.getText().toString().trim();
                NewPw = newPw.getText().toString().trim();
                ConNewPw = conNewPw.getText().toString().trim();

                if(NewPw.equals(ConNewPw)){
                    JSONObject jsonObject = new JSONObject();
                    try {
                        jsonObject.put("USN", usn);
                        jsonObject.put("Password", Password);
                        jsonObject.put("new_password", NewPw);
                        jsonObject.put("confirm_new_password", ConNewPw);

                        Log.d("asdf1", jsonObject.toString());
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                    if (Password.length() > 0) {
                        try {
                            Log.d("asdf2", jsonObject.toString());
                            result = new PostJSON().execute("http://teame-iot.calit2.net/heartdog/app/change_pwd", jsonObject.toString()).get();
                            Log.d("asdf3", result);
                        } catch (ExecutionException e) {
                            e.printStackTrace();
                        }catch (Exception e) {
                            Log.d("asdf411", e.toString());
                            e.printStackTrace();
                        }
                    }
                    try {
                        JSONObject json_data = new JSONObject(result);
                        Log.d("asdf5", "receive json: " + json_data.toString());
                        result_code = (json_data.optString("result_code"));
                        Log.d("asdf6", "result_code: " + result_code);

                    } catch (Exception e) {
                        Log.e("Fail 3", e.toString());
                    }
                    if(result_code.equals("0")){
                        Toast.makeText(PwChange.this, "Password change complete", Toast.LENGTH_SHORT).show();
                        home = new Intent(getApplicationContext(), Home.class);
                        startActivity(home);
                    }
                    else if(result_code.equals("1")){
                        Toast.makeText(PwChange.this, "Please check your password", Toast.LENGTH_SHORT).show();
                    }
                }else{
                    Toast.makeText(PwChange.this, "Please check new password.",Toast.LENGTH_SHORT).show();
                }
            }
        });
    }

    public static boolean validatePassword(String pwStr) { // 값을 비교해주는 함수
        Matcher matcher = VALID_PASSWOLD_REGEX_ALPHA_NUM.matcher(pwStr);
        return matcher.matches();
    }
}
