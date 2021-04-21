<template>
  <div class="uk-section uk-section-secondary">
    <div class="uk-container">
      <h3>Musegit</h3>
      <div class="uk-grid-match uk-child-width-1-3@m" uk-grid>
        <div>
          <p>Work better, Compose together.</p>
        </div>

        <div>
          <p>
            Réalisé par Maël "Steel" Acier, Samuel "July" Benhaim, et Hugo "Le
            Comploteur" Klein.
          </p>
        </div>

        <div>
          <ul class="uk-nav uk-nav-default">
            <li class="uk-nav-header">Liens</li>

            <li>
              <a href="https://github.com/JulyAzanan/Projet-Web">
                <span
                  class="uk-margin-small-right"
                  uk-icon="icon: github"
                ></span>
                Github
              </a>
            </li>

            <li>
              <a href="mailto:foo@example.com">
                <span class="uk-margin-small-right" uk-icon="icon: mail"></span>
                Contact
              </a>
            </li>

            <li :class="{ 'uk-active': $route.path === '/about' }">
              <router-link to="/about">
                <span class="uk-margin-small-right" uk-icon="icon: question">
                </span>
                À propos
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
