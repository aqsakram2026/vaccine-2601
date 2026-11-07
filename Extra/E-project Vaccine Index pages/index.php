<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Vaccine | Vaccination Management System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        :root {
            --teal: #319b91;
            --dark: #183b4e;
            --light: #eff9f7;
        }

        body {
            font-family: Arial, sans-serif;
            color: var(--dark);
            background: #fff;
        }

        .navbar {
            background: #fff;
            padding: 18px 0;
            box-shadow: 0 2px 15px rgba(0,0,0,.05);
        }

        .brand {
            color: var(--teal);
            font-size: 27px;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar .nav-link {
            color: var(--dark);
            font-weight: 500;
            margin: 0 7px;
        }

        .navbar .nav-link:hover {
            color: var(--teal);
        }

        .btn-teal {
            background: var(--teal);
            color: #fff;
            border: 1px solid var(--teal);
            padding: 11px 23px;
            border-radius: 7px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-teal:hover {
            background: #24796f;
            color: #fff;
        }

        .btn-outline-teal {
            color: var(--teal);
            border: 1px solid var(--teal);
            padding: 11px 23px;
            border-radius: 7px;
            text-decoration: none;
            display: inline-block;
        }

        .hero {
            background: linear-gradient(120deg, #f0faf8, #ffffff);
            padding: 85px 0;
        }

        .hero h1 {
            font-size: clamp(36px, 4.5vw, 58px);
            line-height: 1.2;
            font-weight: 750;
        }

        .hero h1 span {
            color: var(--teal);
        }

        .hero p {
            color: #647785;
            font-size: 17px;
            line-height: 1.8;
        }

        .hero-image {
            width: 100%;
            max-height: 410px;
            object-fit: cover;
            border-radius: 24px;
        }

        .section-padding {
            padding: 75px 0;
        }

        .section-title {
            font-weight: 700;
            color: var(--dark);
        }

        .service-card {
            height: 100%;
            padding: 30px 24px;
            border: 1px solid #e6efed;
            border-radius: 14px;
            transition: .25s;
            background: #fff;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(30, 90, 80, .09);
        }

        .service-icon {
            width: 60px;
            height: 60px;
            border-radius: 13px;
            background: var(--light);
            color: var(--teal);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .service-card p {
            color: #71808a;
            line-height: 1.7;
        }

        .about-box {
            background: var(--light);
            border-radius: 18px;
            padding: 42px;
        }

        .about-box li {
            margin-bottom: 14px;
        }

        .cta {
            background: var(--dark);
            color: #fff;
            padding: 55px 25px;
            border-radius: 18px;
        }

        footer {
            background: #142f40;
            color: #d7e3e8;
            padding: 35px 0;
        }

        footer a {
            color: #d7e3e8;
            text-decoration: none;
        }

        footer a:hover {
            color: #fff;
        }

        @media (max-width: 767px) {
            .hero {
                padding: 50px 0;
            }

            .hero-image {
                margin-top: 30px;
            }

            .about-box {
                padding: 25px;
            }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">

        <a class="brand" href="index.php">
            <i class="bi bi-shield-plus"></i> E-Vaccine
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#services">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#hospitals">Hospitals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
            </ul>

            <div class="d-flex gap-2">
                <a href="login.php" class="btn-outline-teal">Login</a>
                <a href="register.php" class="btn-teal">Register</a>
            </div>
        </div>
    </div>
</nav>


<!-- HERO SECTION -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">

            <div class="col-lg-6">
                <span class="badge rounded-pill mb-3 p-2"
                      style="background:#d9f3ee;color:#24796f;">
                    <i class="bi bi-heart-pulse"></i>
                    Your Health, Our Priority
                </span>

                <h1>
                    Protect Your Family with
                    <span>Smart Vaccination</span>
                </h1>

                <p class="mt-4">
                    Manage vaccination records, explore hospital services,
                    and keep track of important vaccination appointments
                    through one convenient platform.
                </p>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="register.php" class="btn-teal">
                        Get Started <i class="bi bi-arrow-right"></i>
                    </a>

                    <a href="#services" class="btn-outline-teal">
                        Explore Services
                    </a>
                </div>

                <div class="mt-4 text-secondary">
                    <i class="bi bi-shield-check text-success"></i>
                    Convenient &nbsp;
                    <i class="bi bi-calendar-check text-success ms-2"></i>
                    Organized &nbsp;
                    <i class="bi bi-hospital text-success ms-2"></i>
                    Connected Care
                </div>
            </div>

            <div class="col-lg-6">
                <img
                    src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBwgHBgkIBwgKCgkLDRYPDQwMDRsUFRAWIB0iIiAdHx8kKDQsJCYxJx8fLT0tMTU3Ojo6Iys/RD84QzQ5OjcBCgoKDQwNGg8PGjclHyU3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3Nzc3N//AABEIALcAwwMBIgACEQEDEQH/xAAbAAACAgMBAAAAAAAAAAAAAAAEBQMGAAECB//EAEIQAAEDAgQDBQUFBgUDBQAAAAEAAgMEEQUSITETQVEGIjJhcRSBkaGxM0JSwdEjJDQ1cvAHFWJz4YKy8RYlVGOi/8QAGQEAAwEBAQAAAAAAAAAAAAAAAgMEAQAF/8QAKBEAAwACAgEDAwQDAAAAAAAAAAECAxEhMRIEQVETMmEUIkKxI0Nx/9oADAMBAAIRAxEAPwCmdncVjw97qesaZcOqbCoibu3/AFt6OG/nqnn+W1UNVIKYieEMD43tOkjDs4fD3KnxMe5zYo2lz3nK1o3JOwXpeF0LsNwunoHSl5BzuN9Q475T08lNOF32WXnWPlE1HTwYhScMkxSgeIDUHzVZxGXEsKrOGKZ00bSbNADHeoNrFXmnpI6gh4daW+pHP1Cbw0jiLzQtluLXWPDSfKGzni0VPCq2rxPD3QGhqYIntOeWR/iNtrWTbsjhEVFMwSRix5EbaKfGaqSnkjp4Iskjte+CGgLeD1bzUBtQwNkjN9NiFilJjP4jsPimPGgaGsfe1huASAugo2QinY2FvgYLM/pubfJdhNfZ552sOy0Fs7LDgeRBVPgKNkQdR4CgoJCqQqXBv5tD71C/dSYP/OIfelLsYui7c10FoLoKsmMI0UR/iB6KcbKJ/wDED0XGnfJYt8lgWnHLhohJ9kadkLONELOFkvNL50xk8SAqt0mg0SxfZt9FiyL7NvosXBFA/wAJcLZi+IzYrka9tC0BjT+N17H3AH4q1Y3R3nBg+zLu8OhXXYOkpuzeHzwUcntDqmXivLm8M7WAt1806qa7Dap442aCbb9oLX969LHrxPPz+ToS0TJI3ZQLi6dQVwjORznf9TbhYzD7tMlOWv8AO+h94UEruEbVNPO3zDczfr+SNpMCapB/tMchs4RP8mvyn4bIf2GCWcGNmWQ+EFvP1CFaKSQgwz5Xk7ZHAfROsBpjC50so1vZuh/NLrGh2PNW+DuppOHStcbvkj8dzuP+PzQQ6+5Pmke1tadS4OuOoFkqraY00xA1Y7Vp/JS3PuUKtkTVs7Llq2dkARFIganRhRryg5/CUFBSKJOa6wf+bQepXEviKzCz/wC60/8AUk/yG+xfR+QXQUbdguwrSY6Ub/4geikB1CGnkZG8Ple2NjdS5xAA+Kx8dnLkIGywquYh2woIHujpiamTqO60e8pFN2xxEyaTwtB2bHFoPe7UoHllBrE2egHZDy6gqpUfaXEnPHEMb29DH+ifUWJtr2ju988m309xWfUl8HPHS7OJh3kun8ZTKcFkhBBB80tqfEgo2SSL7NqxRxfZhaXGibD5P3UNcbX0RrJfaJmwANkY3RzyL2Qfsrw7KNk0wml4ZzIcdVL7KssTU8o3hFD7dXVcOHyvglpS0Sd4t3BsRb0KMxCfEMEjjlraoTU735Bnjz2Pra49UN2Sl4PbLEoj4Zor+9pH6lWTH6BuIYVVUrhfO3M3yI1H0XpYbVNeR5fqMPhvxMoaWJxE0scRe8ZhwwcqOG1juDzSLsnVuqcJhbIbyQExP9QU9vY3RZFqmgMenKaMDbVQf0YR9P0WV0HHpnW8bNW+qyo0jHkVKX3F/IJTW0NXBXAfgtovEqfg1BLfC/X380JyUzWhm9nEmyCqPs3Ix6DqvsygoORLLut4d/Mqf+pal5rih/mNN/uJK7HIv7T3Qu7qMbLrUenNWkpt8jY2mR7sjWDMXE6ALxrtb2uqMVrXsYXR0UbrRRnd3+p3U+XJekdqqsxUrKRo1lvnPkLafNUCWhpjMXcMX8xsp81exVgx7WxJSSVNSbsact9dE9pMOc8jM31vqfcmdFDHlDW29wTiCmAbpzU62yjxSEzKOWOGwuWjqdkwwmSrp5AWABvUlHGmLBcC45pRLNIybuO9EX2sxra0XKeb22KJxaBIG6kc0mqxZ+U7jdbwmre4tubkSd5G4+wMq8zdpG5k3e1skpeL0ARfZhaWRnuBYuM8Ql1LaTQWAREcdhZN5qdlQMzbNf8AiCWysdA8NlbboRsU2sfgHOTzQhw+Tg9vof8AXmb/APg/nZX6S2hOw3Xmj5h/6zoph/8AJjb8XAL0x+5HMHdNwPgX6lcorGDZaPtJiVCfBIRKz6H6J+XDnsTZV/GS2k7R4XVgZS8mJx/ED/4T5/pZV5OdUQ4uHUk0nep3X3DT8tVxA+4PpZSwDMzJ1QMDu4UkcFuYKgSQu+8k8zDE8sdyTKnlDJxc2vousRpRIwuYLOj1Hol2gpYjkQdT9mUY/QfNCTeAqaxyEku6jpP46n/3ApJ/EVDSn9/p/wDcCSuxyPQGnQLq4sAeq5b4Quh7laiRlR7X11MzEYacy99kYuPU3VarD+0zA2BW/wDEOoxNmKVMlAaeSFmUNjeN3iwOa5FhbZDPMsuHxTyx8FxiaXx+ItNhpdTZE+z0cWktBlHUZXAXvdWigfnavKazG6GiqYzURTPY5xaXtdsRa+nvC9G7MTU9XBHPSzcWnkFxfcIJlyFVJjLEnuZTuy8xqkGLSupqaGYMtmIbf6fmrdLScaJzVVe2V46KFojc/wDaNHdbdda9wYoLwGfivklc21mXI8rgJzjLy+WO+4iAKS4ayeOkfxaKen4wja10o3s4H3aApnisgfMbW0FtCuXQnJ2QR+ALa1Ge4Fi0AtkZsAFLJGyeIskbmaeSEp5RK0G1iDYo+PZek+VySJ65R59jWDVGE41SVLjxKZ1SxzZehzDRy9Gk0fYfVR1NLHWU0lPM3Mx7beh5H813IbgHnzSolS+BuTI8mtlX7dM/cIKlvip6hrvcf+bJ/GfaKaOf8bQUB2igNVg1ZE3xcJzm+o1HzAWdl6gT4HSuHJuX4KrvGvwyX/Z/1f0Nac6EG3vSpznwudE95bZx96cMbZ7Xc7pJ2kmZFUQQ/iDnu/L6JWxpj5Mgzg3I1CZ0VYKhrCfEq82QyNAGy6FbDhjTNM4BvTmTyAWU+NsKVt8BuKQtiqnhux1Smo8JXTMbgxF5c8lkrtmnot1AswqOqmuijwqOxDN4ioIf4mH/AHAiJ/GUPH/ERf1t+qnQxHoLPAFjn8ON7+YaSFzF9mz0XY10Oo6K7tcEvT5PN6uVs1a7PbXUkjminzUP+VyuzR8Vgu5jnbhcY3h9VQYw4whr43DuhxIuOWyq1RiUDJODiFHUQvbe/wCyzX9CFE0z1Z8Wh7QYJSzztLSOoY9ocArZh2GQUoAbFGxx1JY0C/wVUwKsZO2OWHNZpynM0tPzV2pZw9rbLpfsZkXAUDyHuVfx6upaCriNRE6QEXOU+HzT2eUQRSTEFwY0uyjc+SENLTYlAJqimDnkNPebcgja112TnhAY3MvyroDp4HOkqHguMDnMcGuO5y3v8SuKkjLfNe/yR0UcscTnVBbxXm5a3Zg5NH973S+oOq5Q4XIjLk+pbZuM9wLFw06BaR6F7GzJjS17cw/ZygEO5OB2ViY7KG+arscBqsBp5C0slbGJG38wHW+ae0LjNQQvOhy6joV6dpJ6RFL2thzdlxKLs9FuE91bfqEAYE85nlp2tYjqknYb9nQVFKd6eYtt0CeBt5AeQO/RVbstV00eMYiG1EWRxBceILNcSRlv120802ftaF1901+S6gaemqr+Px8eud1jaG7e/wDNPiXNF73vr+qrs8hmnkePvuuPRT29dD5XyJKjFRTNMTISXjQl4sEmmkmrakyzyF5Gg5AegVmxTD2VLDJGMsgGo/Eq/wAPJyt5FTZKp9l2BQlx2cub3bA2KYUGJd0U9Ubs2BHJAHZRPaCDrYjZIqWuR61S0MK6B0D9e8x2rXjmg2fbxf1t+qKoqxpj9mqxmhdz/Ceqgqqd9NNGS7PGXjI8cxf5eiFMRUOWXuL7KP0UoNlDCbwsPVq7uryJ9kOI0sdbSvieG5rdxx+65ecYkx1PI6Oo7jmnvB+havTie6Te1tbqr4vijq2bgR34Db26u9UnKlop9PTT6KjheIUxcY4WyyWdvFE54+QVyw98zmDJBJraxcMvy3XVAGFgsAPIJvEBbRKnH7lF5eNaOI4HSNtM7XoDZM6GKGMhmUd4WNkM3RFxu7rT0TpRJVNirEI+HK9hFrfRIqhupKtuMQcaA1EWpb4m9QqpUHvXWZFoCSAPsFijJ1WknyGFmwSpdW4OziMYx8ZkZlYLANEr2t05aNCc0kfDga1V/svCaegnuSX8cg35BzWu+uZPI3m2q9MkC2aXWpfAow9Y517eWqw4r3aCpbJimHYQXlkFVxJp8psXsYG9y/Qlwv1AKOwrBcLlkqpaKFkMThw3QRhuQHS+gHp/ZVf7bxunqqOow2Uf5hREujDtGuB0c13kRf0NjyRWBY9g0ETpa2OqoKx9hNDNFJqbbAtBDx5glY2zdBssT8K/dIJA+mmDuGy1uEW2u0a7EEWGwsbaGwhzgDX6LisxA4lXsqYonw00LCyLityue51ruLTqBYWF+pQdZUZGEi2t0m6GStkGK4jkYWMNj1TPD8Ajq8HZKXFlS7vB3I+qqjW+01jGDd7wF6fTx8GmjiGzWgIMX+RvYV14dHntdSS0kzo5mFjhyO3uQTt16TV08NTEY54myNPIj6Ks4jgEUT/3WcNBNhHKb2WXj+B+L1CfZWb2UtPO6J992HxNOxR+KYQKGibUiYOJflLHNsfUJPCC+UMG7uqQ5KVSpF8w2vhq6ZuQ5XDTL0RnlaxVYpHshaGxPc1m4A3KZNrJI25i02A+KZOXXDI7x7fBPjdR7Ph0hb4n9we/f5XVNLu/ZM8bxOOvkbFFq2MXJ/1cx9EpAvKgvIqfBTgxuZ5HuHyhjBfmnEM4IFlXWN/Zb2A1Lr2sswzF4qhzeDJxGn7wGhCzz0bWPZbGnZTNPdKDhlBaLohrwdk1MlaDqSYXsdL6XVb7Q0XslRxWC0El7H8J6J3G6zgVPV07K6jfBIbB40PQ8ija8pF9M8/c9oJBOqxczslgmfFKI2vYbODnagrFKNLVhLy2asHIthPv74/JGiRL8PAbPJl2Mbf+4oo7n1K9UkZPxVpwkLSc1h9VHE8MeCUVq7vFuW/xK0wWHDWiTO0Xcd1mIzuaxtL4bDvJoyMF9zyHJIMcmLMRnB6i3wSsr8UFHLB5Z7aXuEorp7krqpqrapXK/Oc3ne6guyuZ0POyVIanFWzHww94+vJehPylupsAdT0XnPZnHocML45xkaT41YsUxNldS8OlkyxkZnyDm3oFRhtKfyJuW6C67E+IwsoO84j7bcN93MoZkbi82OaQ/RBUVTDFBYPaLbXTCmqYgySOOI5g1rnSE3zEhZ5eT5N14rgFxCmZUQPhliBJ8DvwlL8I7Nva3i1r7PItkZsNdrp61hIBlN3KaSYQwueG3AFlz0wldJa2J6zD6eiqo/Zy6723c1zr2R0ULZaYskbmaUsqrtqHTz1DRHe7i42sOl+idRECMFpu06g309yDS6OeygV1DJhOM1FM7WCovJC7r1HuKnhYTJYq0Y7hoxOiLWaVER4kLujgNj5HZea4hj01IJYfZ3tqomOLonDVlhc5vIAKeocvgtxZFU8jjHa4TFuGUr9xmnd0b0963QEU4aAbW0VR7P4nxLmQSGaQ5nFwsbqzQT90F+Rturroab3yNWmi4UEuexvdN4nKlUeIMz5Y35nfG3uCs1FOZGAyZr+Yt8k2K9iXJI1a5FQuvpzOyCY4ECyIiN1RJNSJpcPoql5mno+LI4C7+ui2sGy2mC9FfoYpGzyseSDkjIt6yfojm0jSe/nceiSxYwIhLPI5l3SZG3HJrW/mSmWH417RIG8WNrdybWRzkh/tR14rX7mNIoWxDusay/Ib/Fbl0bbqgK3EzDUltO6ORhAN0HJi8xfq1nuv+qx+ohPTZs4KfQ6gec7WAXuq924YYZoahos2UFjv6gdPkttxyeCUuY2O/mCULV4hPiLWx1wZJC12bKWAa9UnLnipaQyMFp7Kw95eTrudEa3B8RdGXugyNa2+aQgfLdMoXQMxLDxHFGWOmtbbXKSPnZOJ28aF7CftbtJ6G2n0U0pMbe5K9S4S2kLG1TWyyyC8ZGwsL/mu5KkmkDm7yS5SOhDSfrZHyATVs1GTke1gfEejmjLb5NKBAD657S3KXhk7R0cDZw+Z+C1cGcPsWGd4kLQbHMMp6HkrhgjjJTZnC0n3h0KRYlQBksgDQHg3aeTgjsBqnse2KTbkTyCOeGdS2ixgA2BUcrWTh8BBykam/mNPesllDWWPhJ3B3XE0xjhc+JodpoNgmtpiUUztRFT4k52F1L3iCGQZnxuylxadL+XJWuivHSDIwuYxoDQDoRZUinnFR2jMDyHOkc4vtoL3urfitK+LBpqVshIqIzEHNHhuNT8EubKMka0vkBh7RWrDBVMfTyg6sePmD081JjnZ6mxWOoqKKSCmrqiHgyT8PMS3odfLdKHYHPNSRxSYi+bhEFr5YwXAW6jkmcAqGNja2YtyNy6DQ/FD5bZvh8HmkOENwiuNLik00UrfDZ4DXjyICeUz8IaRrG94/Ec7lY8Y7N02Nhn+YOkcGOzixFr+eiGOBVGGx5qSFs0LRtG0Nf8ADY/VKqd9D4tG6WvisBBTzG237ItHz0Temq3yaGAtPmR+SCoI5qmESRxPAOliAD8Eyhpp2kZo3fJHMUBbkPgJdumEOhCDp4ZR9wj1R7IZDa7VRMsltolJWLoQOt41tN8WK2jzmCNj4YgYy57R45DoCSSbNHUknX4IyPuDe5VfixNpF99d1P8A5iLCxsvOdtnp+CQ9D1w96Tx12d291MJcxKELQaXjmuc45IYv0vy63siIIKmYXihkLfxW0+K1Jsx6QHiUhp4BUMbd8L2yN9WkFXAFry6SJ12k6/kfeD81W3Uz5GZZO6DuN0T2dqjTPdS1LAIAbMc113fC2yOdy+RWSfNftCsQjtXjMDlfoHgbG2x8iNP/AAsMdqiGVzQ43vm3D2k636EKWtq6NriIi+U8gALD3oQYg1jTmZYb2OqL32JUU0HVLXRymOWMyRfccRqB081w2nZYuicG21QUXaKcxtGSIjYXGY/FB1WNTvvZzWnkctkapI5YqHz8QbDEHyWa372Y2946qrdoO01ZVNkpMPDGtdpxXg3A9EDPM+d15HFxHMlQGFLvI3wh8YUuWSYPTCmna57nPme4Oe8nUkK75zM1ri7N8NPgqhTx5Sx3RWKjmGQXQQvkO1voYtYLarWUX0XPFBAstB9yqBLDImaKZrOaFifZEseiQtknsjZO8wiN9t2utf1XDYpI4mZnOLje5/v+9V3JI5sWdptlIJ9OaN4kc7Wi+eMjMCP76p0E+Ta5OILi9yTrz9EYw3QrYshybjkev92RMY+eqYhTJVi6A0Wlxx4ZF2cx2R5DKF+U9ZGg/VFQ9ku0ThrFDG0bcSYX+V16GyUM4bhs5gQ89c1ncabWvdKXpMetj/1WQqMPZfEmfxFZTs8m5nfomuHdnYnSgVdXLI0a2Z3f1PzRj6gyHKCCTsCUfg0XHmfTyOyyMbms2xWrDjXsY8+RrsiqIqDDgBFA3Nvmd3j81rjz1jBJKcjeQO6YT4FTyTmaZ0rnN0ALtETFSxxts3b8XNE1xqQFXO6K+aOR5JZqOl90FBFGa/vwtc/8Th4f7sri2nDLlttRq4jVDx4XA1xc6MSF2js+/wDwlvAtD8fqfF8iHggSG+UD7obsB0SrE5Gxteb2VrfgsRzBssjW9MyW1XY3D6rWaWoN/wD7iPolvDWtDf1EdlRglBiY0G/dClDMydyf4fUpFqbEKyJw2PEa/wCrbrbexNTHAGw4rIZRzliBB9QP1S3gsJepx/IlZHqiGU19OqZx9lcUitxa2kcfJjgjYsBqWjv1EI/6Su+jXwF+oj5E8VNluLXstMq4wfEL35lPJuz0s0eRtcI77lsf/KDHYeJx79bL6tjaLrVgYL9RJFHVZhuD6FStqFPD2Kp4XZmYhVDqO7qiBgVNHccWZ565mj8kX0qRn1oBm1GqKiqLrqHBoA/vSTN6d4H8lMcLpo+9x5ug0BXLHYLywSslu0jlZSYXM0OfRkuAzF7CdvQLmKhic2/GcRtsApmU9DG9sklQ5jmGwNwmzNLsVVw1wMo2XFwLA8uinY2yGhnjc0OZI11xobqdsri4NuC49E4lJrLFyS4HXdaXHHnBxVz8NgeBrlCBkqXyd69jfVYsRbekFpbBZajL3pDdo3TfAM9UBUwvcyQ6tsVpYkV2h08Sy3YdiftB9mqog2dvNpuCj8t2rFibAmjjh7qMvc0933rFi0wwyAjUXKjD3EkWsFixYcdDu3LQDfe6zOSbW181ixacdA38Zv5dFvTksWLjjogHdatbwm3VYsWHEU0pawgjdCte0nu891pYsCXRO1hI0+KldC1zB16rFi04CqGywAva+4sbhCSzMqeGZge7tZYsWe4WuCeAxgMa0uAaLBRVs9VDmfTzOsBZvqVixagWginlqKqFszz3nb7ctFixYuMP/9k="
                    alt="Healthcare professional providing medical care"
                    class="hero-image"
                    onerror="this.style.display='none';">
            </div>

        </div>
    </div>
</section>


<!-- SERVICES -->
<section class="section-padding" id="services">
    <div class="container">

        <div class="text-center mb-5">
            <p class="text-uppercase fw-bold"
               style="color:var(--teal);letter-spacing:2px;">
                Our Services
            </p>
            <h2 class="section-title">Healthcare Made Easier</h2>
            <p class="text-secondary mt-3">
                Simple tools to help you manage your vaccination journey.
            </p>
        </div>

        <div class="row g-4">

            <div class="col-md-6 col-lg-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                    <h5 class="fw-bold">Vaccination Appointments</h5>
                    <p>
                        Organize vaccination appointments and keep track
                        of your scheduled visits.
                    </p>
                    <a href="register.php"
                       class="text-decoration-none fw-bold"
                       style="color:var(--teal);">
                        Get Started →
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-journal-medical"></i>
                    </div>
                    <h5 class="fw-bold">Vaccination Records</h5>
                    <p>
                        Keep vaccination information organized and
                        accessible through your account.
                    </p>
                    <a href="login.php"
                       class="text-decoration-none fw-bold"
                       style="color:var(--teal);">
                        View Records →
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-hospital"></i>
                    </div>
                    <h5 class="fw-bold">Hospital Portal</h5>
                    <p>
                        Provide hospitals with a dedicated portal to
                        manage their vaccination services.
                    </p>
                    <a href="hospital_login.php"
                       class="text-decoration-none fw-bold"
                       style="color:var(--teal);">
                        Hospital Login →
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ABOUT -->
<section class="section-padding" id="about"
         style="background:#fafdfc;">
    <div class="container">
        <div class="row align-items-center g-4">

            <div class="col-lg-6">
                <div class="about-box">
                    <i class="bi bi-shield-check"
                       style="font-size:55px;color:var(--teal);"></i>

                    <h3 class="fw-bold mt-3">
                        A Smarter Way to Manage Vaccination
                    </h3>

                    <p class="text-secondary mt-3">
                        Our platform brings vaccination-related services
                        together to make record management and access
                        more convenient.
                    </p>

                    <ul class="mt-4">
                        <li>Easy access to your account</li>
                        <li>Organized vaccination information</li>
                        <li>Dedicated hospital login portal</li>
                        <li>Convenient access across devices</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 ps-lg-5">
                <p class="fw-bold text-uppercase"
                   style="color:var(--teal);">
                    About E-Vaccine
                </p>

                <h2 class="section-title">
                    Supporting Healthier Communities
                </h2>

                <p class="text-secondary mt-3"
                   style="line-height:1.9;">
                    E-Vaccine is designed to support patients and hospitals
                    with a convenient digital vaccination management
                    experience. Its goal is to make information easier
                    to organize and access.
                </p>

                <a href="register.php" class="btn-teal mt-3">
                    Join Our Platform
                </a>
            </div>

        </div>
    </div>
</section>


<!-- HOSPITAL SECTION -->
<section class="section-padding" id="hospitals">
    <div class="container">
        <div class="cta text-center">

            <i class="bi bi-hospital"
               style="font-size:50px;color:#70d6c7;"></i>

            <h2 class="fw-bold mt-3">
                Are You a Hospital Partner?
            </h2>

            <p class="mt-3 mb-4"
               style="color:#d0dce2;">
                Access your hospital portal to manage your
                vaccination services and records.
            </p>

            <a href="hospital_login.php"
               class="btn btn-light me-2 mb-2">
                Hospital Login
            </a>

            <a href="hospital_register.php"
               class="btn-teal mb-2">
                Register Hospital
            </a>

        </div>
    </div>
</section>


<!-- CONTACT -->
<section class="section-padding" id="contact">
    <div class="container text-center">

        <p class="fw-bold text-uppercase"
           style="color:var(--teal);">
            Contact Us
        </p>

        <h2 class="section-title">Need Assistance?</h2>

        <p class="text-secondary mt-3">
            For help with your account or vaccination services,
            contact your registered hospital or system administrator.
        </p>

        <a href="mailto:support@evaccine.example"
           class="btn-outline-teal mt-2">
            <i class="bi bi-envelope"></i> Contact Support
        </a>

    </div>
</section>


<!-- FOOTER -->
<footer>
    <div class="container">
        <div class="row align-items-center gy-3">

            <div class="col-md-6">
                <h4 class="fw-bold">
                    <i class="bi bi-shield-plus"></i> E-Vaccine
                </h4>
                <p class="mb-0 small">
                    Smart vaccination management for everyone.
                </p>
            </div>

            <div class="col-md-6 text-md-end">
                <a href="index.php" class="me-3">Home</a>
                <a href="#about" class="me-3">About</a>
                <a href="#services">Services</a>
                <p class="mt-3 mb-0 small">
                    &copy; <?php echo date("Y"); ?>
                    E-Vaccine. All rights reserved.
                </p>
            </div>

        </div>
    </div>
</footer>


<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
```

