//using a function change my paragrah
function changeText() 
    {
        document.getElementById("myParagraph").innerHTML = "The text has been changed!";
    }

//using a function to hide content
function hidebtn() 
{
    var x = document.getElementById("myDIV");
    
    if (x.style.display === "none") 
        {
            x.style.display = "block";
        } 
        
        else 
            {
                x.style.display = "none";
            }
    
}

//using a function to change my images
function imagemod() 
    {
        document.getElementById("myImg1").src = "picks/Digimon.jpg";
        document.getElementById("myImg2").src = "picks/Digimon title.jpg";
        document.getElementById("myImg3").src = "picks/digimon-adventure-tr.jpg";
    }

//using a function to change a buttons text
function changebtn(elem) 
    {
        elem.innerHTML = "Hello World!";
    }

//using a function to display onblur event
function validateField()
    {
        var field = document.getElementById("requiredField");
        var errorMessage = document.getElementById("errorMessage");
        if (field.value.trim() === "") 
            {
                errorMessage.style.display = "inline";
            } 
        
            else 
                {
                    errorMessage.style.display = "none";
                }
    }

function fetchText()
    {
        getText("fetch_info.txt");

        async function getText(file) 
            {
                let myObject = await fetch(file);
                let myText = await myObject.text();
                document.getElementById("demo").innerHTML = myText;
            }
    }

    /* new stuff for monitoring center */
    /* ------------------- SECURITY ALERT POPUP ------------------- */
function checkSecurityAlert() {
    fetch("get_alert.php")
        .then(res => res.text())
        .then(msg => {
            if (msg.trim() !== "") showPopup(msg);
        });
}

function showPopup(message) {
    const div = document.createElement("div");
    div.className = "alert-popup";
    div.textContent = message;

    document.body.appendChild(div);

    setTimeout(() => div.remove(), 5000);
}

setInterval(checkSecurityAlert, 3000);


/* ------------------- SEARCH FILTER ------------------- */
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("searchInput");

    if (!input) return;

    input.addEventListener("keyup", () => {
        const filter = input.value.toLowerCase();
        const cards = document.querySelectorAll(".user-card");

        cards.forEach(card => {
            const name = card.querySelector(".name").textContent.toLowerCase();
            const dept = card.querySelector(".department").textContent.toLowerCase();

            card.style.display =
                name.includes(filter) || dept.includes(filter)
                    ? "block"
                    : "none";
        });
    });
});
