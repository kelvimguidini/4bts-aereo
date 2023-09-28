<?php $v->layout("theme/_theme"); ?>
<?php $v->start("styles"); ?>
<link rel="stylesheet" href="<?= asset("aereo/css/air.css"); ?>">
<link rel="stylesheet" href="<?= asset("booking/css/form.css"); ?>">
<?php $v->end(); ?>

<div id="app" class="container body">

    <div ref="responseData" hidden><?= $data; ?></div>

    <div v-show="showValidation" class="modal">
        <div class="modal-content">
            <span class="close" v-on:click="showValidation = false">&times;</span>
            <h2>Verifique esse(s) erro(s)</h2>
            <ul style="margin: 10px 0;">
                <li v-for="(error, index) in validateErrors">
                    <b>{{ error.field }}:</b> <span>{{error.message}}</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="container main-header">
        <div class="background"><img src="https://cdn.pixabay.com/photo/2016/07/22/03/27/rio-de-janeiro-1534089_960_720.jpg" alt=""></div>
        <div class="row cover">
            <img src="https://cdn.pixabay.com/photo/2016/07/22/03/27/rio-de-janeiro-1534089_960_720.jpg" alt="">
        </div>
    </div>

    <h1 style="text-align: center; margin-top: 20px;">Reserva De Voos</h1>
    <div class="form-container">
        <form action="">
            <div class="form-contex" v-for="(item, index) in flightBookings" :key="index">
                <span class="form-title"> {{index + 1}} Passageiro(a) {{item.peopleType}}</span>

                <div class="form-group">
                    <div class="input-group">
                        <label for="name">Nome *</label>
                        <input type="text" id="name" name="name" v-model="item.name">
                    </div>
                    <div class="input-group">
                        <label for="lastName">Sobrenome *</label>
                        <input type="text" id="lastName" name="lastName" v-model="item.lastName">
                    </div>
                    <div class="input-group">
                        <label for="email">E-mail *</label>
                        <input type="email" id="email" name="email" v-model="item.email">
                    </div>
                    <div class="input-group">
                        <div>
                            <label for="gender">Gênero *</label>
                            <select name="gender" v-model="item.gender">
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="documentType">Tipo documento *</label>
                        <select name="documentType" v-model="item.documentType">
                            <option value="RG">RG</option>
                            <option value="CPF">CPF</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="document">Número Documento *</label>
                        <input type="number" id="document" name="document" v-model="item.document">
                    </div>
                    <div class="input-group">
                        <label for="birthDate">Data Nascimento *</label>
                        <input type="date" id="birthDate" name="birthDate" v-model="item.birthDate">
                    </div>
                    <div class="input-group">
                        <label for="phone">Telefone *</label>
                        <input type="tel" id="phone" name="phone" maxlength="15" pattern="\(\d{2}\)\s*\d{5}-\d{4}" v-model="item.phone">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="country">País *</label>
                        <input type="text" id="country" name="country" v-model="item.country">
                    </div>
                    <div class="input-group">
                        <label for="zipCode">CEP *</label>
                        <input type="number" id="zipCode" name="zipCode" v-model="item.zipCode">
                    </div>
                    <div class="input-group">
                        <label for="state">Estado *</label>
                        <input type="text" id="state" name="state" v-model="item.state">
                    </div>
                    <div class="input-group">
                        <label for="city">Cidade *</label>
                        <input type="text" id="city" name="city" v-model="item.city">
                    </div>
                </div>

                <div class="form-group grid-4-col-3 form-group-space">
                    <div class="input-group grid-4-space-1">
                        <label for="street">Rua *</label>
                        <input type="text" id="street" name="street" v-model="item.street">
                    </div>
                    <div class="input-group grid-4-space-1">
                        <label for="number">Número *</label>
                        <input type="number" id="number" name="number" v-model="item.number">
                    </div>
                    <div class="input-group grid-4-space-2">
                        <label for="complement">Complemento *</label>
                        <input type="text" id="complement" name="complement" v-model="item.complement">
                    </div>
                </div>
            </div>

            <div class="form-contex">
                <div class="btn-submit">
                    <input type="submit" @click="booking($event)" value="Salvar">
                </div>
            </div>
        </form>
    </div>
</div>

<?php $v->start("scripts"); ?>
<script src="<?= asset("booking/js/main.js"); ?>"></script>
<script src="<?= asset("booking/js/form.js"); ?>"></script>
<?php $v->end(); ?>