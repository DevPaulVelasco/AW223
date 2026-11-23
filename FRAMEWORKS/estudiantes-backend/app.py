from flask import Flask

app=Flask(__name__)

@app.route('/')
def inicio():
    return "HELLO WORLD FLASK"

    
@app.route('/soyotraruta', method='post')
def otro():
    return "HELLO WORLD OTRO"

if __name__=='__main__':
    app.run(debug=True)